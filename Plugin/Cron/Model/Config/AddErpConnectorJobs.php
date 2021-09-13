<?php
namespace MageSuite\ErpConnector\Plugin\Cron\Model\Config;

class AddErpConnectorJobs
{
    /**
     * @var \MageSuite\ErpConnector\Api\SchedulerRepositoryInterface
     */
    protected $schedulerRepository;

    public function __construct(\MageSuite\ErpConnector\Api\SchedulerRepositoryInterface $schedulerRepository)
    {
        $this->schedulerRepository = $schedulerRepository;
    }

    public function afterGetJobs(\Magento\Cron\Model\Config $subject, $result)
    {
        $schedulers = $this->schedulerRepository->getList();

        if (!$schedulers->getTotalCount()) {
            return $result;
        }

        $jobs = [];

        foreach ($schedulers->getItems() as $scheduler) {
            $methodName = sprintf(\MageSuite\ErpConnector\Helper\Configuration::CRON_JOB_METHOD_FORMAT, $scheduler->getId());

            $jobs[$methodName] = [
                'name' => $methodName,
                'instance' => \MageSuite\ErpConnector\Cron\Process::class,
                'method' => $methodName,
                'schedule' => $scheduler->getCronExpression()
            ];
        }

        $result[\MageSuite\ErpConnector\Helper\Configuration::CRON_GROUP_ID] = array_merge(
            $result[\MageSuite\ErpConnector\Helper\Configuration::CRON_GROUP_ID],
            $jobs
        );

        return $result;
    }
}
