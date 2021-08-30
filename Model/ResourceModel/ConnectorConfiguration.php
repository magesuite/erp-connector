<?php
namespace MageSuite\ErpConnector\Model\ResourceModel;

class ConnectorConfiguration extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('erp_connector_connector_configuration', \MageSuite\ErpConnector\Api\Data\ConnectorConfigurationInterface::ENTITY_ID);
    }
}
