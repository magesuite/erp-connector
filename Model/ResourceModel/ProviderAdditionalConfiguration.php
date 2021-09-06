<?php
namespace MageSuite\ErpConnector\Model\ResourceModel;

class ProviderAdditionalConfiguration extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('erp_connector_provider_additional_configuration', \MageSuite\ErpConnector\Api\Data\ProviderAdditionalConfigurationInterface::ID);
    }

    public function getProviderNamesWithSpecificAdditionalConfig($codes)
    {
        $connection = $this->getConnection();

        $query = $connection
            ->select()
            ->from(['pac' => $connection->getTableName('erp_connector_provider_additional_configuration')], ['entity_id', 'key'])
            ->joinLeft(['p' => $connection->getTableName('erp_connector_provider')], 'pac.provider_id = p.provider_id', 'name')
            ->where('pac.key in (?)', $codes);

        try {
            return $connection->fetchAll($query);
        } catch (\Exception $e) {
            return null;
        }
    }
}
