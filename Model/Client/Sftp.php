<?php
namespace MageSuite\ErpConnector\Model\Client;

class Sftp extends \Magento\Framework\DataObject implements ClientInterface
{
    /**
     * @var \Magento\Framework\Filesystem\Io\SftpFactory;
     */
    protected $sftpFactory;

    /**
     * @var \MageSuite\ErpConnector\Model\Command\LogErrorMessage
     */
    protected $logErrorMessage;

    protected $connection = null;

    public function __construct(
        \Magento\Framework\Filesystem\Io\SftpFactory $sftpFactory,
        \MageSuite\ErpConnector\Model\Command\LogErrorMessage $logErrorMessage,
        array $data = []
    ) {
        parent::__construct($data);

        $this->sftpFactory = $sftpFactory;
        $this->logErrorMessage = $logErrorMessage;
    }

    public function checkConnection()
    {
        $connection = $this->getConnection();
        $location = sprintf(self::LOCATION_FORMAT, $this->getData('username'), $this->getData('host'));

        if (!$connection->cd($this->getData('destination_dir'))) {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Unable to detect a directory "%1" at a remote SFTP location %2.', $this->getData('destination_dir'), $location));
        }

        if (!$connection->cd($this->getData('source_dir'))) {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Unable to detect a directory "%1" at a remote SFTP location %2.', $this->getData('source_dir'), $location));
        }

        $this->closeConnection($connection);
    }

    public function sendItems($provider, $items)
    {
        foreach ($items as $item) {
            $this->sendItem($provider, $item);
        }

        return $this;
    }

    protected function sendItem($provider, $item)
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
        $sourceDir = $this->getData('source_dir');

        try {
            $connection = $this->getConnection();

            foreach ($files as $fileName => $content) {
                $this->validateFileOnExternalServerDirectory($sourceDir, $fileName, $content, $provider->getName());
                $this->validateFileOnExternalServerDirectory($this->getData('destination_dir'), $fileName, $content, $provider->getName());

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
        }

        return true;
    }

    public function downloadItems($provider)
    {
        $downloaded = [];

        $location = sprintf(self::LOCATION_FORMAT, $this->getData('username'), $this->getData('host'));

        try {
            $connection = $this->getConnection();

            $sourceDir = $this->getData('source_dir');
            $destinationDir = $this->getData('destination_dir');

            $this->validateDirectoryOnExternalServer($sourceDir, $provider);
            $this->validateDirectoryOnExternalServer($destinationDir, $provider);

            $connection->cd($sourceDir);
            $files = $connection->ls();

            foreach ($files as $file) {
                $fileName = $connection->getCleanPath($file['text']);

                if (!$this->validateFileName($fileName)) {
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
            throw new \MageSuite\ErpConnector\Exception\FilesNotFound(__(
                'Can\'t detect any valid files at a "%1" remote SFTP location %2.',
                $location,
                $provider->getName()
            ));
        }

        return $downloaded;
    }

    protected function validateFileName($fileName)
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

    protected function validateDirectoryOnExternalServer($directory, $providerName)
    {
        $connection = $this->getConnection();
        $location = sprintf(self::LOCATION_FORMAT, $this->getData('username'), $this->getData('host'));

        if ($connection->cd($directory)) {
            return true;
        } else {
            throw new \MageSuite\ErpConnector\Exception\DirectoryNotFound(__('Unable to detect a directory "%1" at a "%2" remote SFTP location %3.', $directory, $providerName, $location));
        }
    }

    protected function validateFileOnExternalServerDirectory($directory, $fileName, $content, $providerName) //phpcs:ignore
    {
        $connection = $this->getConnection();

        $location = sprintf(self::LOCATION_FORMAT, $this->getData('username'), $this->getData('host'));

        if ($connection->cd($directory)) {
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
                    throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('A file "%1" with the same name and without content already exists at a "%2" remote SFTP location %3 (%4).', $directory, $providerName, $location));
                }

                if ($destinationFileContent === $content) {
                    throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('A file "%1" with the same name and same content already exists at a "%2" remote SFTP location %3 (%4).', $directory, $providerName, $location));
                }

                throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('A file "%1" with the same name and different content already exists at a "%2" remote SFTP location %3 (%4).', $directory, $providerName, $location));
            }
        } else {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Unable to detect a directory "%1" at a "%2" remote SFTP location %3.', $directory, $providerName, $location));
        }

        return true;
    }

    private function getConnection()
    {
        if ($this->connection !== null) {
            return $this->connection;
        }

        $sftpConfig = [
            'host' => $this->getData('host'),
            'username' => $this->getData('username'),
            'password' => $this->getData('password'),
            'timeout' => $this->getData('timeout') ?? 15,
        ];

        $connection = $this->sftpFactory->create();
        $connection->open($sftpConfig);

        $this->connection = $connection;

        return $this->connection;
    }

    private function closeConnection($connection)
    {
        $connection->close();
        $this->connection = null;
    }

    public function validateProcessedFile($fileName)
    {
        try {
            $connection = $this->getConnection();
            $connection->ls($this->getData('destination_dir'));

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
