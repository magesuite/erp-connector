<?php
namespace MageSuite\ErpConnector\Model\ResourceModel\ProviderAdditionalConfiguration;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \MageSuite\ErpConnector\Model\Data\ProviderAdditionalConfiguration::class,
            \MageSuite\ErpConnector\Model\ResourceModel\ProviderAdditionalConfiguration::class
        );
    }
}
