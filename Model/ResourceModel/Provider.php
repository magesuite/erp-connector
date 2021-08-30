<?php
namespace MageSuite\ErpConnector\Model\ResourceModel;

class Provider extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('erp_connector_provider', \MageSuite\ErpConnector\Api\Data\ProviderInterface::ENTITY_ID);
    }
}
