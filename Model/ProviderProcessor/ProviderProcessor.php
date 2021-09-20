<?php

namespace MageSuite\ErpConnector\Model\ProviderProcessor;

class ProviderProcessor extends \Magento\Framework\DataObject
{
    protected $scheduler;

    public function setScheduler($scheduler)
    {
        $this->scheduler = $scheduler;
    }

    public function execute()
    {
        return true;
    }
}
