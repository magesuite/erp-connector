<?php
namespace MageSuite\ErpConnector\Plugin\Cron\Observer\ProcessCronQueueObserver;

class AddPendingJobs
{
    /**
     * @var \MageSuite\ErpConnector\Model\Command\Cron\GetErpConnectorJobs
     */
    protected $getErpConnectorJobs;

    public function __construct(\MageSuite\ErpConnector\Model\Command\Cron\GetErpConnectorJobs $getErpConnectorJobs)
    {
        $this->getErpConnectorJobs = $getErpConnectorJobs;
    }

    public function beforeProcessPendingJobs(\Magento\Cron\Observer\ProcessCronQueueObserver $subject, string $groupId, array $jobsRoot, int $currentTime)
    {
        if ($groupId != \MageSuite\ErpConnector\Helper\Configuration::CRON_GROUP_ID) {
            return [$groupId, $jobsRoot, $currentTime];
        }

        $jobs = $this->getErpConnectorJobs->execute();

        if (empty($jobs)) {
            return [$groupId, $jobsRoot, $currentTime];
        }

        $jobsRoot = array_merge($jobsRoot, $jobs);
        return [$groupId, $jobsRoot, $currentTime];
    }
}
