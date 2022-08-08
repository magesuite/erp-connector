<?php

namespace MageSuite\ErpConnector\Model\Schedule;

class Jobs implements \MageSuite\Schedule\Model\Schedule\JobsGroupInterface
{
    protected \MageSuite\ErpConnector\Model\ResourceModel\Cron $cronResource;

    public function __construct(\MageSuite\ErpConnector\Model\ResourceModel\Cron $cronResource)
    {
        $this->cronResource = $cronResource;
    }

    public function execute()
    {
        return $this->cronResource->getErpConnectorSchedulerJobs();
    }
}
