<?php
namespace MageSuite\ErpConnector\Model\ResourceModel\SchedulerConnectorConfiguration;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(
            \MageSuite\ErpConnector\Model\Data\SchedulerConnectorConfiguration::class,
            \MageSuite\ErpConnector\Model\ResourceModel\SchedulerConnectorConfiguration::class
        );
    }
}
