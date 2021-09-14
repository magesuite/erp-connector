<?php
namespace MageSuite\ErpConnector\Plugin\Cron\Model\Config;

class AddSchedulerJobs
{
    /**
     * @var \MageSuite\ErpConnector\Model\ResourceModel\Cron
     */
    protected $resourceModel;

    /**
     * @var null|array
     */
    protected $schedulerJobs = null;

    public function __construct(\MageSuite\ErpConnector\Model\ResourceModel\Cron $resourceModel)
    {
        $this->resourceModel = $resourceModel;
    }

    public function afterGetJobs(\Magento\Cron\Model\Config $subject, $result)
    {
        $schedulerJobs = $this->getSchedulerJobs();

        if (empty($schedulerJobs)) {
            return $result;
        }

        $result[\MageSuite\ErpConnector\Helper\Configuration::CRON_GROUP_ID] = array_merge(
            $result[\MageSuite\ErpConnector\Helper\Configuration::CRON_GROUP_ID],
            $schedulerJobs
        );

        return $result;
    }

    protected function getSchedulerJobs()
    {
        if ($this->schedulerJobs !== null) {
            return $this->schedulerJobs;
        }

        $result = [];

        $schedulerJobs = $this->resourceModel->getAllSchedulerJobs();

        if (empty($schedulerJobs)) {
            return $result;
        }

        foreach ($schedulerJobs as $job) {
            $methodName = sprintf(\MageSuite\ErpConnector\Helper\Configuration::CRON_JOB_METHOD_FORMAT, $job['id']);

            $result[$methodName] = [
                'name' => $methodName,
                'instance' => \MageSuite\ErpConnector\Cron\Process::class,
                'method' => $methodName,
                'schedule' => $job['cron_expression']
            ];
        }

        $this->schedulerJobs = $result;
        return $this->schedulerJobs;
    }
}
