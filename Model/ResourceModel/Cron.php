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

    public function getAllSchedulerJobs()
    {
        $select = $this->connection
            ->select()
            ->from(['ecs' => $this->connection->getTableName('erp_connector_scheduler')], ['id', 'cron_expression']);

        return $this->connection->fetchAll($select);
    }

    public function getSchedulerPendingCronJobCodes()
    {
        $select = $this->connection
            ->select()
            ->from(['cs' => $this->connection->getTableName('cron_schedule')], ['job_code'])
            ->where('cs.job_code LIKE ?', \MageSuite\ErpConnector\Helper\Configuration::CRON_JOB_PREFIX_FORMAT)
            ->where('cs.status = ?', \Magento\Cron\Model\Schedule::STATUS_PENDING);

        return $this->connection->fetchCol($select);
    }

}
