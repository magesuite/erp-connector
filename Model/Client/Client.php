<?php

namespace MageSuite\ErpConnector\Model\Client;

class Client extends \Magento\Framework\DataObject
{
    /**
     * @var \MageSuite\ErpConnector\Model\Command\AddAdminNotification
     */
    protected $addAdminNotification;

    /**
     * @var \MageSuite\ErpConnector\Logger\Logger
     */
    protected $logger;

    public function __construct(
        \MageSuite\ErpConnector\Model\Command\AddAdminNotification $addAdminNotification,
        \MageSuite\ErpConnector\Logger\Logger $logger,
        array $data = []
    ) {
        parent::__construct($data);

        $this->addAdminNotification = $addAdminNotification;
        $this->logger = $logger;
    }

    public function checkConnection()
    {
        throw new \MageSuite\ErpConnector\Exception\ConnectionFailed(__('Not possible to test connection.'));
    }

    public function logErrorMessage($title, $message)
    {
        $this->logger->error($message);
        $this->addAdminNotification->execute($title, $message);
    }
}
