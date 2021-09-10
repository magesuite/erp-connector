<?php

namespace MageSuite\ErpConnector\Model\ProviderProcessor;

interface ProviderProcessorInterface
{
    /**
     * @param \MageSuite\ErpConnector\Model\Data\Scheduler $scheduler
     * @return void
     */
    public function setScheduler($scheduler);

    /**
     * @return bool
     */
    public function execute();
}
