<?php

namespace MageSuite\ErpConnector\Model\ResourceModel;

class GetRawConnectorConfigurationValue
{
    protected \Magento\Framework\App\ResourceConnection $resourceConnection;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    public function execute(string $connectorId, string $name): ?string
    {
        $connection = $this->resourceConnection->getConnection();

        $tableName = $this->resourceConnection->getTableName('erp_connector_connector_configuration');

        $select = $connection->select()
            ->from($tableName, ['value'])
            ->where('connector_id = ?', $connectorId)
            ->where('name = ?', $name)
            ->limit(1);

        $result = $connection->fetchOne($select);

        return $result ? $result : null;
    }
}
