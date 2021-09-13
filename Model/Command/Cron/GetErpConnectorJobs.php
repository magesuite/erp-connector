<?php
namespace MageSuite\ErpConnector\Model\Command\Cron;

class GetErpConnectorJobs
{
    /**
     * @var \MageSuite\ErpConnector\Model\ResourceModel\Cron
     */
    protected $resourceModel;

    protected $jobs = [];

    public function __construct(\MageSuite\ErpConnector\Model\ResourceModel\Cron $resourceModel)
    {
        $this->resourceModel = $resourceModel;
    }

    public function execute()
    {
        if (!empty($this->jobs)) {
            return $this->jobs;
        }

        $jobCodes = $this->resourceModel->getJobs();

        $jobs = [];

        foreach ($jobCodes as $jobCode) {
            $jobs[$jobCode] = [
                'name' => $jobCode,
                'instance' => \MageSuite\ErpConnector\Cron\Process::class,
                'method' => $jobCode,
                'schedule' => '* * * * *'
            ];
        }

        $this->jobs = $jobs;
        return $this->jobs;
    }
}
