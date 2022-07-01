<?php
namespace MageSuite\ErpConnector\Cron;

class Process
{
    const LOCK_NAME = 'erp_connector_scheduler';

    protected $handlerClass = \MageSuite\ErpConnector\Model\Queue\SchedulerHandler::class;

    protected \MageSuite\ErpConnector\Helper\Configuration $configuration;

    protected \MageSuite\Queue\Service\Publisher $queuePublisher;

    protected \MageSuite\ErpConnector\Service\Scheduler\Processor $schedulerProcessor;

    public function __construct(
        \MageSuite\ErpConnector\Helper\Configuration $configuration,
        \MageSuite\Queue\Service\Publisher $queuePublisher,
        \MageSuite\ErpConnector\Service\Scheduler\Processor $schedulerProcessor,
        \Magento\Framework\Lock\Backend\Database $databaseLocker
    ) {
        $this->configuration = $configuration;
        $this->queuePublisher = $queuePublisher;
        $this->schedulerProcessor = $schedulerProcessor;
    }

    /**
     * etc/crontab.xml does not allow passing arguments to invoked methods
     * so for executing scheduler logic we use part of invoked method name as scheduler identifier
     * calling erp_connector_scheduler_2 will invoke scheduler with id: 2
     * @param $name
     * @param array $arguments
     */
    public function __call($name, array $arguments)
    {
        if (!$this->configuration->isEnabled()) {
            return;
        }

        if (preg_match('/scheduler_([0-9+])/', $name)) {
            $schedulerId = $this->getSchedulerId($name);
            $this->process($schedulerId);
        }
    }

    protected function getSchedulerId($name)
    {
        $jobCodeParts = explode('_', $name);
        return end($jobCodeParts);
    }

    protected function process($schedulerId)
    {
        $method = $this->configuration->getSchedulingMethod();

        switch ($method) {
            case \MageSuite\ErpConnector\Model\Source\SchedulingMethod::METHOD_RABBITMQ:
                $this->queuePublisher->publish($this->handlerClass, $schedulerId);
                break;
            default:
                $this->schedulerProcessor->execute($schedulerId);
                break;
        }
    }

    public function execute() //phpcs:ignore
    {
    }
}
