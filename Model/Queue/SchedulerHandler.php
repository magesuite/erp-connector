<?php
namespace MageSuite\ErpConnector\Model\Queue;

class SchedulerHandler
{
    /**
     * @var \MageSuite\ErpConnector\Api\SchedulerRepositoryInterface
     */
    protected $schedulerRepository;

    /**
     * @var \MageSuite\ErpConnector\Service\Processor
     */
    protected $schedulerProcessor;

    /**
     * @var \MageSuite\ErpConnector\Logger\Logger
     */
    protected $logger;

    public function __construct(
        \MageSuite\ErpConnector\Api\SchedulerRepositoryInterface $schedulerRepository,
        \MageSuite\ErpConnector\Service\Processor $schedulerProcessor,
        \MageSuite\ErpConnector\Logger\Logger $logger
    ) {
        $this->schedulerRepository = $schedulerRepository;
        $this->schedulerProcessor = $schedulerProcessor;
        $this->logger = $logger;
    }

    public function execute($schedulerId)
    {
        try {
            $scheduler = $this->schedulerRepository->getById($schedulerId);
            $this->schedulerProcessor->execute($scheduler);
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
