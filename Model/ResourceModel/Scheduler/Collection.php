<?php
namespace MageSuite\ErpConnector\Model\ResourceModel\Scheduler;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(
            \MageSuite\ErpConnector\Model\Data\Scheduler::class,
            \MageSuite\ErpConnector\Model\ResourceModel\Scheduler::class
        );
    }
}
