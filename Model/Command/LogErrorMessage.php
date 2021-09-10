<?php
namespace MageSuite\ErpConnector\Model\Command;

class LogErrorMessage
{
    const MESSAGE_WITH_DATA_FORMAT = "%s\nData: %s";

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
        \MageSuite\ErpConnector\Logger\Logger $logger
    ) {
        $this->addAdminNotification = $addAdminNotification;
        $this->logger = $logger;
    }

    public function execute($title, $message, $data = null)
    {
        if ($data !== null) {
            $message = sprintf(self::MESSAGE_WITH_DATA_FORMAT, $message, var_export($data, true));
        }

        $this->logger->error($message);
        $this->addAdminNotification->execute($title, $message);

    }
}
