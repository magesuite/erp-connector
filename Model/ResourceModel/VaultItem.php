<?php
namespace MageSuite\ErpConnector\Model\ResourceModel;

class VaultItem extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('erp_connector_vault', \MageSuite\ErpConnector\Model\Data\VaultItem::ID);
    }
}
