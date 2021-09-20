<?php
namespace MageSuite\ErpConnector\Model\Queue;

class SchedulerHandler
{
    /**
     * @var \MageSuite\ErpConnector\Service\Scheduler\Processor
     */
    protected $schedulerProcessor;

    /**
     * @var \MageSuite\ErpConnector\Logger\Logger
     */
    protected $logger;

    public function __construct(
        \MageSuite\ErpConnector\Service\Scheduler\Processor $schedulerProcessor,
        \MageSuite\ErpConnector\Logger\Logger $logger
    ) {
        $this->schedulerProcessor = $schedulerProcessor;
        $this->logger = $logger;
    }

    public function execute($schedulerId)
    {
        try {
            $this->schedulerProcessor->execute($schedulerId);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
