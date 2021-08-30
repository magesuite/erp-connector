<?php
namespace MageSuite\ErpConnector\Model\ResourceModel\ConnectorConfiguration;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(
            \MageSuite\ErpConnector\Model\Data\ConnectorConfiguration::class,
            \MageSuite\ErpConnector\Model\ResourceModel\ConnectorConfiguration::class
        );
    }
}
