<?php
namespace MageSuite\ErpConnector\Cron;

class Process
{
    protected $handlerClass = \MageSuite\ErpConnector\Model\Queue\SchedulerHandler::class;

    /**
     * @var \MageSuite\ErpConnector\Helper\Configuration
     */
    protected $configuration;

    /**
     * @var \MageSuite\Queue\Service\Publisher
     */
    protected $queuePublisher;

    /**
     * @var \MageSuite\ErpConnector\Service\Scheduler\Processor
     */
    protected $schedulerProcessor;

    public function __construct(
        \MageSuite\ErpConnector\Helper\Configuration $configuration,
        \MageSuite\Queue\Service\Publisher $queuePublisher,
        \MageSuite\ErpConnector\Service\Scheduler\Processor $schedulerProcessor
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
        $method = $this->configuration->getSchedulerMethod();

        switch ($method) {
            case \MageSuite\ErpConnector\Model\Source\SchedulerMethod::METHOD_RABBITMQ:
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
