<?php
namespace MageSuite\ErpConnector\Model\ResourceModel\Provider;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \MageSuite\ErpConnector\Model\Data\Provider::class,
            \MageSuite\ErpConnector\Model\ResourceModel\Provider::class
        );
    }
}
