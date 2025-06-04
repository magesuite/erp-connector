<?php

declare(strict_types=1);

namespace MageSuite\ErpConnector\Model\Client;

class Sftp extends FileClient implements ClientInterface
{
    protected ?\Magento\Framework\Filesystem\Io\Sftp $connection = null;

    public function __construct(
        protected \MageSuite\ErpConnector\Helper\Configuration $configuration,
        protected \Magento\Framework\Event\Manager $eventManager,
        protected \MageSuite\ErpConnector\Model\Command\FormatDirectoryName $formatDirectoryName,
        protected \Magento\Framework\Filesystem\Io\SftpFactory $sftpFactory,
        protected \MageSuite\ErpConnector\Model\Framework\Filesystem\Io\SftpProxyFactory $sftpProxyFactory,
        protected \MageSuite\ErpConnector\Model\Command\LogErrorMessage $logErrorMessage,
        protected array $data = []
    ) {
        parent::__construct($eventManager, $formatDirectoryName, $data);
    }

    public function checkConnection(): void
    {
        $connection = $this->getConnection();
        $location = sprintf(self::LOCATION_FORMAT, $this->getData('username'), $this->getData('host'));

        if (!$connection->cd($this->getDestinationDirectory())) {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Unable to detect a directory "%1" at a remote SFTP location %2.', $this->getDestinationDirectory(), $location));
        }

        if (!$connection->cd($this->getSourceDirectory())) {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Unable to detect a directory "%1" at a remote SFTP location %2.', $this->getSourceDirectory(), $location));
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
                    throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Unable to upload a file "%1" to "%2" at a "%3" remote SFTP location %4.', $sourceDir, $provider->getName(), $location));
                }

                if ($this->getData('skip_validation')) {
                    return $result;
                }

                $exportedFileContent = $connection->read($fileName);

                if (!$exportedFileContent || $exportedFileContent !== $content) {
                    $connection->rm($fileName);
                    $this->closeConnection($connection);

                    throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Unable to write a content to a file "%1" at a "%2" remote SFTP location %3.', $sourceDir, $provider->getName(), $location));
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
            $this->validateDirectoryExist($destinationDir, $provider->getName());

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
                    sprintf(self::MOVED_FILE_NAME_FORMAT, $destinationDir, date(self::FILE_PREFIX_DATETIME_FORMAT), $fileName)
                );

                if (!$fileMoved) {
                    throw new \MageSuite\ErpConnector\Exception\RemoteImportFailed(__(
                        'Can\'t move a file "%1" from a source directory "%2" to a destination directory "%3" at a "%4" remote SFTP location %5.',
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
            throw new \MageSuite\ErpConnector\Exception\MissingDownloadData(__(
                'Can\'t detect any valid files at a "%1" remote SFTP location %2.',
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

        if (!in_array($fileName, $this->getData('allowed_files'))) {
            return false;
        }

        return false;
    }

    public function validateDirectoryExist(string $directory, string $providerName): bool
    {
        $connection = $this->getConnection();
        $location = sprintf(self::LOCATION_FORMAT, $this->getData('username'), $this->getData('host'));

        if ($connection->cd($directory)) {
            return true;
        } else {
            throw new \MageSuite\ErpConnector\Exception\DirectoryNotFound(__(
                'Unable to detect a directory "%1" at a "%2" remote SFTP location %3.',
                $directory,
                $providerName,
                $location
            ));
        }
    }

    protected function validateFile(string $directory, string $fileName, string $content, string $providerName): bool //phpcs:ignore
    {
        $connection = $this->getConnection();

        $location = sprintf(self::LOCATION_FORMAT, $this->getData('username'), $this->getData('host'));

        if ($connection->cd($directory)) {
            try {
                $files = $connection->ls();
            } catch (\Exception $e) {
                $files = [];
            }

            if (!is_array($files)) {
                return true;
            }

            foreach ($files as $file) {
                if ($file['text'] !== $fileName) {
                    continue;
                }

                $destinationFileContent = $connection->read($fileName);

                if (!$destinationFileContent) {
                    throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__(
                        'A file "%1" with the same name and without content already exists at a "%2" remote SFTP location %3 (%4).',
                        $directory,
                        $providerName,
                        $location
                    ));
                }

                if ($destinationFileContent === $content) {
                    throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__(
                        'A file "%1" with the same name and same content already exists at a "%2" remote SFTP location %3 (%4).',
                        $directory,
                        $providerName,
                        $location
                    ));
                }

                throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__(
                    'A file "%1" with the same name and different content already exists at a "%2" remote SFTP location %3 (%4).',
                    $directory,
                    $providerName,
                    $location
                ));
            }
        } else {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__(
                'Unable to detect a directory "%1" at a "%2" remote SFTP location %3.',
                $directory,
                $providerName,
                $location
            ));
        }

        return true;
    }

    public function getConnection(): \Magento\Framework\Filesystem\Io\Sftp
    {
        if ($this->connection !== null) {
            return $this->connection;
        }

        if ($this->getData('use_proxy')) {
            $connection = $this->sftpProxyFactory->create();
        } else {
            $connection = $this->sftpFactory->create();
        }

        $connection->open($this->getClientConfiguration());

        $this->connection = $connection;

        return $this->connection;
    }

    public function getClientConfiguration(): array
    {
        return [
            'host' => $this->getData('host'),
            'username' => $this->getData('username'),
            'password' => $this->getData('password'),
            'timeout' => $this->getData('timeout') ?? 15,
            'proxy' => $this->configuration->getSftpConnectorProxy()
        ];
    }

    public function closeConnection(\Magento\Framework\Filesystem\Io\Sftp $connection): void
    {
        $connection->close();
        $this->connection = null;
    }

    public function validateProcessedFile(string $fileName): bool
    {
        try {
            $connection = $this->getConnection();
            $connection->ls($this->getDestinationDirectory());

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

