<?php
namespace MageSuite\ErpConnector\Model\ResourceModel\Connector;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(
            \MageSuite\ErpConnector\Model\Data\Connector::class,
            \MageSuite\ErpConnector\Model\ResourceModel\Connector::class
        );
    }
}
