<?php
namespace MageSuite\ErpConnector\Model\Client;

class Ftp extends \Magento\Framework\DataObject implements ClientInterface
{
    /**
     * @var \Magento\Framework\Filesystem\Io\FtpFactory;
     */
    protected $ftpFactory;

    /**
     * @var \MageSuite\ErpConnector\Model\Command\LogErrorMessage
     */
    protected $logErrorMessage;

    protected $connection = null;

    public function __construct(
        \Magento\Framework\Filesystem\Io\FtpFactory $ftpFactory,
        \MageSuite\ErpConnector\Model\Command\LogErrorMessage $logErrorMessage,
        array $data = []
    ) {
        parent::__construct($data);

        $this->ftpFactory = $ftpFactory;
        $this->logErrorMessage = $logErrorMessage;
    }

    public function checkConnection()
    {
        $connection = $this->getConnection();
        $location = sprintf(self::LOCATION_FORMAT, $this->getData('user'), $this->getData('host'));

        if (!$connection->cd($this->getData('destination_dir'))) {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Unable to detect a directory "%1" at a remote FTP location %2.', $this->getData('destination_dir'), $location));
        }

        if (!$connection->cd($this->getData('source_dir'))) {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Unable to detect a directory "%1" at a remote FTP location %2.', $this->getData('source_dir'), $location));
        }

        $connection->close();
    }

    public function sendItem($provider, $data)
    {
        $content = $data['content'] ?? null;

        if (!$content) {
            $this->logErrorMessage->execute(
                sprintf(self::ERROR_MESSAGE_TITLE_FORMAT, $provider->getName()),
                'Missing content',
                $data
            );
            return $this;
        }

        $fileName = $data['file_name'] ?? null;

        if (!$fileName) {
            $this->logErrorMessage->execute(
                sprintf(self::ERROR_MESSAGE_TITLE_FORMAT, $provider->getName()),
                'Missing file name',
                $data
            );
            return $this;
        }


        $location = sprintf(self::LOCATION_FORMAT, $this->getData('user'), $this->getData('host'));
        $sourceDir = $this->getData('source_dir');

        try {
            $connection = $this->getConnection();

            $this->validateFileOnExternalServerDirectory($sourceDir, $fileName, $content, $provider->getName());
            $this->validateFileOnExternalServerDirectory($this->getData('destination_dir'), $fileName, $content, $provider->getName());

            $connection->cd($sourceDir);
            $result = $connection->write($fileName, $content);

            if (!$result) {
                throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Unable to upload a file "%1" to "%2" at a "%3" remote FTP location %4.', $sourceDir, $provider->getName(), $location));
            }

            if ($this->getData('skip_validation')) {
                return $result;
            }

            $exportedFileContent = $connection->read($fileName);

            if (!$exportedFileContent || $exportedFileContent !== $content) {
                $connection->rm($fileName);
                $connection->close();

                throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Unable to write a content to a file "%1" at a "%2" remote FTP location %3.', $sourceDir, $provider->getName(), $location));
            }

            $connection->close();
        } catch (\Exception $e) {
            $this->logErrorMessage->execute(
                sprintf(self::ERROR_MESSAGE_TITLE_FORMAT, $provider->getName()),
                $e->getMessage(),
                $data
            );
        }

        return $this;
    }

    protected function validateFileOnExternalServerDirectory($directory, $fileName, $content, $providerName) //phpcs:ignore
    {
        $connection = $this->getConnection();

        $location = sprintf(self::LOCATION_FORMAT, $this->getData('user'), $this->getData('host'));

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
                    throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('A file "%1" with the same name and without content already exists at a "%2" remote FTP location %3 (%4).', $directory, $providerName, $location));
                }

                if ($destinationFileContent === $content) {
                    throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('A file "%1" with the same name and same content already exists at a "%2" remote FTP location %3 (%4).', $directory, $providerName, $location));
                }

                throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('A file "%1" with the same name and different content already exists at a "%2" remote FTP location %3 (%4).', $directory, $providerName, $location));
            }
        } else {
            throw new \MageSuite\ErpConnector\Exception\RemoteExportFailed(__('Unable to detect a directory "%1" at a "%2" remote FTP location %3.', $directory, $providerName, $location));
        }

        return true;
    }

    private function getConnection()
    {
        if ($this->connection !== null) {
            return $this->connection;
        }

        $ftpConfig = [
            'host' => $this->getData('host'),
            'user' => $this->getData('username'),
            'password' => $this->getData('password'),
            'passive' => $this->getData('passive_mode'),
        ];

        $port = $this->getData('port');

        if ($port) {
            $ftpConfig['port'] = $port;
        }

        $connection = $this->ftpFactory->create();
        $connection->open($ftpConfig);

        $this->connection = $connection;

        return $this->connection;
    }
}
