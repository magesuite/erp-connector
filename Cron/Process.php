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
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \MageSuite\ErpConnector\Api\SchedulerRepositoryInterface
     */
    protected $schedulerRepository;

    /**
     * @var \MageSuite\Queue\Service\Publisher
     */
    protected $queuePublisher;

    /**
     * \Magento\Cron\Model\ScheduleFactory;
     */
    protected $magentoScheduleFactory;

    /**
     * @var \MageSuite\ErpConnector\Service\Scheduler\Processor
     */
    protected $schedulerProcessor;

    /**
     * @var \MageSuite\ErpConnector\Logger\Logger
     */
    protected $logger;

    public function __construct(
        \MageSuite\ErpConnector\Helper\Configuration $configuration,
        \MageSuite\ErpConnector\Api\SchedulerRepositoryInterface $schedulerRepository,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Cron\Model\ScheduleFactory $magentoScheduleFactory,
        \MageSuite\Queue\Service\Publisher $queuePublisher,
        \MageSuite\ErpConnector\Service\Scheduler\Processor $schedulerProcessor,
        \MageSuite\ErpConnector\Logger\Logger $logger
    ) {
        $this->configuration = $configuration;
        $this->schedulerRepository = $schedulerRepository;
        $this->dateTime = $dateTime;
        $this->magentoScheduleFactory = $magentoScheduleFactory;
        $this->queuePublisher = $queuePublisher;
        $this->schedulerProcessor = $schedulerProcessor;
        $this->logger = $logger;
    }

    public function execute()
    {
        if (!$this->configuration->isEnabled()) {
            return;
        }

        $schedulersToProcess = $this->getSchedulersToProcess();

        foreach ($schedulersToProcess as $scheduler) {
            $this->process($scheduler);
        }
    }

    protected function getSchedulersToProcess()
    {
        $schedulers = $this->schedulerRepository->getList();

        if (!$schedulers->getTotalCount()) {
            throw new \RuntimeException(__('Export schedulers not exist'));
        }

        $currentTimestamp = $this->dateTime->gmtTimestamp();

        $schedulersToProcess = [];

        foreach ($schedulers->getItems() as $scheduler) {
            try {
                $isValid = $this->validateScheduler($scheduler, $currentTimestamp);

                if (!$isValid) {
                    $this->logger->error('Not valid cron expression in schedule, id: ' . $scheduler->getId());
                    continue;
                }
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
                continue;
            }

            $schedulersToProcess[] = $scheduler;
        }

        return $schedulersToProcess;
    }

    protected function validateScheduler($scheduler, $currentTimestamp)
    {
        /** @var \Magento\Cron\Model\Schedule $schedule */
        $schedule = $this->magentoScheduleFactory->create()
            ->setCronExpr($scheduler->getCronExpression())
            ->setJobCode('test_for_check_valid_time_execution')
            ->setStatus(\Magento\Cron\Model\Schedule::STATUS_PENDING)
            ->setCreatedAt(strftime('%Y-%m-%d %H:%M:%S', $this->dateTime->gmtTimestamp()))
            ->setScheduledAt(strftime('%Y-%m-%d %H:%M', $currentTimestamp));

        return $schedule->trySchedule();
    }

    protected function process($scheduler)
    {
        $method = $this->configuration->getSchedulerMethod();

        switch ($method) {
            case \MageSuite\ErpConnector\Model\Source\SchedulerMethod::METHOD_RABBITMQ:
                $this->queuePublisher->publish($this->handlerClass, $scheduler->getId());
                break;
            default:
                $this->schedulerProcessor->execute($scheduler);
                break;
        }
    }
}
