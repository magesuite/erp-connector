<?php

namespace MageSuite\ErpConnector\Model\ResourceModel;

class Cron
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    public function __construct(\Magento\Framework\App\ResourceConnection $resourceConnection)
    {
        $this->connection = $resourceConnection->getConnection();
    }

    public function getErpConnectorSchedulerJobs()
    {
        $select = $this->connection
            ->select()
            ->from(['ecs' => $this->connection->getTableName('erp_connector_scheduler')], ['id', 'cron_expression']);

        return $this->connection->fetchAll($select);
    }
}
