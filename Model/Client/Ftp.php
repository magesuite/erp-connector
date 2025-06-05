<?php

declare(strict_types=1);

namespace MageSuite\ErpConnector\Model\Client;

class Ftp extends FileClient implements ClientInterface
{
    protected ?\Magento\Framework\Filesystem\Io\Ftp $connection = null;

    public function __construct(
        protected \Magento\Framework\Event\Manager $eventManager,
        protected \MageSuite\ErpConnector\Model\Command\FormatDirectoryName $formatDirectoryName,
        protected \Magento\Framework\Filesystem\Io\FtpFactory $ftpFactory,
        protected \MageSuite\ErpConnector\Model\Command\LogErrorMessage $logErrorMessage,
        array $data = []
    ) {
        parent::__construct($eventManager, $formatDirectoryName, $data);
    }

    public function checkConnection(): void
    {
        $connection = $this->getConnection();
        $location = sprintf(self::LOCATION_FORMAT, $this->getData('username'), $this->getData('host'));

        if (!$connection->cd($this->getDestinationDirectory())) {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(sprintf('Unable to detect a directory "%s" at a remote FTP location %s.', $this->getDestinationDirectory(), $location));
        }

        if (!$connection->cd($this->getSourceDirectory())) {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(sprintf('Unable to detect a directory "%s" at a remote FTP location %s.', $this->getSourceDirectory(), $location));
        }

        $this->closeConnection($connection);
    }

    public function sendItems(\MageSuite\ErpConnector\Api\Data\ProviderInterface $provider, array $items): self
    {
        foreach ($items as $item) {
            $this->sendItem($provider, $item);
        }

        return $this;
    }

    protected function sendItem(\MageSuite\ErpConnector\Api\Data\ProviderInterface $provider, array $item): bool
    {
        $files = $item['files'] ?? null;

        if (empty($files)) {
            $this->logErrorMessage->execute(
                sprintf(self::ERROR_MESSAGE_TITLE_FORMAT, $provider->getName()),
                'Missing files data',
                $item
            );
            return false;
        }

        $location = sprintf(self::LOCATION_FORMAT, $this->getData('username'), $this->getData('host'));
        $sourceDir = $this->getSourceDirectory();

        try {
            $connection = $this->getConnection();

            foreach ($files as $fileName => $content) {
                $this->validateFile($sourceDir, $fileName, $content, $provider->getName());
                $this->validateFile($this->getDestinationDirectory(), $fileName, $content, $provider->getName());

                $connection->cd($sourceDir);
                $result = $connection->write($fileName, $content);

                if (!$result) {
                    throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(sprintf('Unable to upload a file to "%s" at a "%s" remote FTP location %s.', $sourceDir, $provider->getName(), $location));
                }

                if ($this->getData('skip_validation')) {
                    return $result;
                }

                $exportedFileContent = $connection->read($fileName);

                if (!$exportedFileContent || $exportedFileContent !== $content) {
                    $connection->rm($fileName);
                    $this->closeConnection($connection);

                    throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(sprintf('Unable to write a content to a file "%s" at a "%s" remote FTP location %s.', $sourceDir, $provider->getName(), $location));
                }
            }

            $this->closeConnection($connection);
        } catch (\Exception $e) {
            $this->logErrorMessage->execute(
                sprintf(self::ERROR_MESSAGE_TITLE_FORMAT, $provider->getName()),
                $e->getMessage(),
                $item
            );

            throw $e;
        }

        return true;
    }

    public function downloadItems(\MageSuite\ErpConnector\Api\Data\ProviderInterface $provider): array
    {
        $downloaded = [];

        $location = sprintf(self::LOCATION_FORMAT, $this->getData('username'), $this->getData('host'));

        try {
            $connection = $this->getConnection();

            $sourceDir = $this->getSourceDirectory();
            $destinationDir = $this->getDestinationDirectory();

            $this->validateDirectoryExist($sourceDir, $provider->getName());
            $this->validateDirectoryExist($destinationDir, $provider->getName(), $this->getData('destination_dir'));

            $connection->cd($sourceDir);
            $files = $connection->ls();

            foreach ($files as $file) {
                $fileName = $connection->getCleanPath($file['text']);

                if (!$this->isValidFileName($fileName)) {
                    continue;
                }

                $downloaded[$fileName] = $connection->read($fileName);

                $fileMoved = $connection->mv(
                    sprintf(self::FILE_PATH_FORMAT, $sourceDir, $fileName),
                    $this->getMovedFilePath($destinationDir, $fileName, (bool)$this->getData('rename_moved_file'))
                );

                if (!$fileMoved) {
                    throw new \MageSuite\ErpConnector\Exception\RemoteImportFailed(sprintf(
                        'Can\'t move a file "%s" from a source directory "%s" to a destination directory "%s" at a "%s" remote FTP location %s.',
                        $fileName,
                        $sourceDir,
                        $destinationDir,
                        $provider->getName(),
                        $location
                    ));
                }
            }

            $this->closeConnection($connection);
        } catch (\Exception $e) {
            $this->logErrorMessage->execute(
                sprintf(self::ERROR_MESSAGE_TITLE_FORMAT, $provider->getName()),
                $e->getMessage()
            );
        }

        if (empty($downloaded)) {
            throw new \MageSuite\ErpConnector\Exception\MissingDownloadData(sprintf(
                'Can\'t detect any valid files at a "%s" remote FTP location %s.',
                $location,
                $provider->getName()
            ));
        }

        return $downloaded;
    }

    public function isValidFileName(string $fileName): bool
    {
        if (empty($fileName) || $fileName == '../') {
            return false;
        }

        $pattern = $this->getData('file_name_pattern');

        if (empty($pattern) || preg_match($pattern, $fileName)) {
            return true;
        }

        return false;
    }

    protected function validateFile(string $directory, string $fileName, string $content, string $providerName): bool //phpcs:ignore
    {
        $connection = $this->getConnection();

        $location = sprintf(self::LOCATION_FORMAT, $this->getData('username'), $this->getData('host'));

        if (!$connection->cd($directory)) {
            throw new \MageSuite\ErpConnector\Exception\DirectoryNotFound(sprintf(
                'Unable to detect a directory "%s" at a "%s" remote FTP location %s.',
                $directory,
                $providerName,
                $location
            ));
        }

        $files = $connection->ls();

        if (!is_array($files)) {
            return true;
        }

        foreach ($files as $file) {
            if ($file['text'] !== $fileName) {
                continue;
            }

            $destinationFileContent = $connection->read($fileName);

            if (!$destinationFileContent) {
                throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(sprintf(
                    'A file "%s" with the same name and without content already exists at a "%s" remote FTP location %s.',
                    $directory,
                    $providerName,
                    $location
                ));
            }

            if ($destinationFileContent === $content) {
                throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(sprintf(
                    'A file "%s" with the same name and same content already exists at a "%s" remote FTP location %s.',
                    $directory,
                    $providerName,
                    $location
                ));
            }

            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(sprintf(
                'A file "%s" with the same name and different content already exists at a "%s" remote FTP location %s.',
                $directory,
                $providerName,
                $location
            ));
        }

        return true;
    }

    public function getConnection(): \Magento\Framework\Filesystem\Io\Ftp
    {
        if ($this->connection !== null) {
            return $this->connection;
        }

        $connection = $this->ftpFactory->create();
        $connection->open($this->getClientConfiguration());

        $this->connection = $connection;

        return $this->connection;
    }

    public function getClientConfiguration(): array
    {
        $configuration = [
            'host' => $this->getData('host'),
            'user' => $this->getData('username'),
            'password' => $this->getData('password'),
            'passive' => $this->getData('passive_mode'),
        ];

        $port = $this->getData('port');

        if ($port) {
            $configuration['port'] = $port;
        }

        return $configuration;
    }

    public function closeConnection(\Magento\Framework\Filesystem\Io\Ftp $connection): void
    {
        $connection->close();
        $this->connection = null;
    }

    public function validateProcessedFile(string $fileName): bool
    {
        try {
            $connection = $this->getConnection();
            $connection->cd($this->getDestinationDirectory());

            $destinationFileContent = $connection->read($fileName);

            if (!empty($destinationFileContent)) {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }
}
