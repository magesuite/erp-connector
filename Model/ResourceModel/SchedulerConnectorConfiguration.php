<?php
namespace MageSuite\ErpConnector\Model\ResourceModel;

class SchedulerConnectorConfiguration extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('erp_connector_scheduler_connector_configuration', \MageSuite\ErpConnector\Model\Data\SchedulerConnectorConfiguration::ID);
    }
}
