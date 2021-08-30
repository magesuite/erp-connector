<?php

namespace MageSuite\ErpConnector\Model\Client;

class Ftp extends Client implements ClientInterface
{
    /**
     * @var \Magento\Framework\Filesystem\Io\FtpFactory;
     */
    protected $ftpFactory;

    public function __construct(
        \Magento\Framework\Filesystem\Io\FtpFactory $ftpFactory,
        array $data = []
    ) {
        parent::__construct($data);

        $this->ftpFactory = $ftpFactory;
    }

    public function checkConnectiona()
    {
        $ftpPath = [
            'host' => $this->getData('host'),
            'user' => $this->getData('username'),
            'password' => $this->getData('password'),
            'passive' => $this->getData('passive_mode'), //todo add config for FTP mode
        ];

        $location = $ftpPath['user'] . '@' .$ftpPath['host'];

        $io = $this->ftpFactory->create();
        $io->open($ftpPath);

        if (!$io->cd($this->getData('destination_dir'))) {
            throw new \Exception( //phpcs:ignore
                __(
                    'Unable to detect a directory "%1" at a remote FTP location %2.',
                    $this->getData('destination_dir'),
                    $location
                )
            );
        }

        if (!$io->cd($this->getData('source_dir'))) {
            throw new \Exception( //phpcs:ignore
                __(
                    'Unable to detect a directory "%1" at a remote FTP location %2.',
                    $this->getData('source_dir'),
                    $location
                )
            );
        }

        $io->close();
    }
}
