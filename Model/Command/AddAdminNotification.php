<?php
namespace MageSuite\ErpConnector\Model\Command;

class AddAdminNotification
{
    /**
     * @var \Magento\AdminNotification\Model\Inbox
     */
    protected $notification;

    public function __construct(\Magento\AdminNotification\Model\Inbox $notification)
    {
        $this->notification = $notification;
    }

    public function execute($title, $description, $severity = \Magento\Framework\Notification\MessageInterface::SEVERITY_CRITICAL)
    {
        try {
            $this->notification->add($severity, $title, $description);
        } catch (\Exception $e) { //phpcs:ignore

        }
    }
}
