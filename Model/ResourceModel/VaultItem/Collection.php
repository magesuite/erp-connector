<?php
namespace MageSuite\ErpConnector\Model\ResourceModel\VaultItem;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(
            \MageSuite\ErpConnector\Model\Data\VaultItem::class,
            \MageSuite\ErpConnector\Model\ResourceModel\VaultItem::class
        );
    }
}
