<?php

namespace MageSuite\ErpConnector\Model\Client;

class Sftp extends Client implements ClientInterface
{
    /**
     * @var \Magento\Framework\Filesystem\Io\SftpFactory;
     */
    protected $sftpFactory;

    public function __construct(
        \Magento\Framework\Filesystem\Io\SftpFactory $sftpFactory,
        array $data = []
    ) {
        parent::__construct($data);

        $this->sftpFactory = $sftpFactory;
    }

    public function checkConnection()
    {
        $sftpPath = [
            'host' => $this->getData('host'),
            'user' => $this->getData('username'),
            'password' => $this->getData('password'),
            'timeout' => 10,
        ];

        $location = $sftpPath['user'] . '@' . $sftpPath['host'];

        $io = $this->sftpFactory->create();
        $io->open($sftpPath);

        if (!$io->cd($this->getData('destination_dir'))) {
            throw new \Exception( //phpcs:ignore
                __(
                    'Unable to detect a directory "%1" at a remote SFTP location %2.',
                    $this->getData('destination_dir'),
                    $location
                )
            );
        }

        if (!$io->cd($this->getData('source_dir'))) {
            throw new \Exception( //phpcs:ignore
                __(
                    'Unable to detect a directory "%1" at a remote SFTP location %2.',
                    $this->getData('source_dir'),
                    $location
                )
            );
        }

        $io->close();
    }
}
