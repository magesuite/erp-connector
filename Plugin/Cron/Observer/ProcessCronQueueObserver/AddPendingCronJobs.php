<?php
namespace MageSuite\ErpConnector\Plugin\Cron\Observer\ProcessCronQueueObserver;

class AddPendingCronJobs
{
    /**
     * @var \MageSuite\ErpConnector\Model\ResourceModel\Cron
     */
    protected $resourceModel;

    public function __construct(\MageSuite\ErpConnector\Model\ResourceModel\Cron $resourceModel)
    {
        $this->resourceModel = $resourceModel;
    }

    public function beforeProcessPendingJobs(\Magento\Cron\Observer\ProcessCronQueueObserver $subject, string $groupId, array $jobsRoot, int $currentTime)
    {
        if ($groupId != \MageSuite\ErpConnector\Helper\Configuration::CRON_GROUP_ID) {
            return [$groupId, $jobsRoot, $currentTime];
        }

        $schedulerPendingJobs = $this->getSchedulerPendingCronJobs();

        if (empty($schedulerPendingJobs)) {
            return [$groupId, $jobsRoot, $currentTime];
        }

        $jobsRoot = array_merge($jobsRoot, $schedulerPendingJobs);
        return [$groupId, $jobsRoot, $currentTime];
    }

    public function getSchedulerPendingCronJobs()
    {
        $result = [];

        $jobCodes = $this->resourceModel->getSchedulerPendingCronJobCodes();

        if (empty($jobCodes)) {
            return $result;
        }

        foreach ($jobCodes as $jobCode) {
            $result[$jobCode] = [
                'name' => $jobCode,
                'instance' => \MageSuite\ErpConnector\Cron\Process::class,
                'method' => $jobCode,
                'schedule' => '* * * * *'
            ];
        }

        return $result;
    }
}
