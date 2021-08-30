<?php
namespace MageSuite\ErpConnector\Model\ResourceModel;

class ProviderAdditionalConfiguration extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('erp_connector_provider_additional_configuration', \MageSuite\ErpConnector\Api\Data\ProviderAdditionalConfigurationInterface::ID);
    }
}
