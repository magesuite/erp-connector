<?php
namespace MageSuite\ErpConnector\Model\ResourceModel;

class Scheduler extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('erp_connector_scheduler', \MageSuite\ErpConnector\Model\Data\Scheduler::ID);
    }
}
