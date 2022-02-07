<?php
namespace MageSuite\ErpConnector\Service\Scheduler;

class Processor
{
    /**
     * @var \MageSuite\ErpConnector\Api\SchedulerRepositoryInterface
     */
    protected $schedulerRepository;

    /**
     * @var \MageSuite\ErpConnector\Api\ProviderRepositoryInterface
     */
    protected $providerRepository;

    /**
     * @var \MageSuite\ErpConnector\Model\SchedulersPool
     */
    protected $schedulersPool;

    /**
     * @var \MageSuite\ErpConnector\Logger\Logger
     */
    protected $logger;

    public function __construct(
        \MageSuite\ErpConnector\Api\SchedulerRepositoryInterface $schedulerRepository,
        \MageSuite\ErpConnector\Api\ProviderRepositoryInterface $providerRepository,
        \MageSuite\ErpConnector\Model\SchedulersPool $schedulersPool,
        \MageSuite\ErpConnector\Logger\Logger $logger
    ) {
        $this->schedulerRepository = $schedulerRepository;
        $this->providerRepository = $providerRepository;
        $this->schedulersPool = $schedulersPool;
        $this->logger = $logger;
    }

    public function execute($schedulerId)
    {
        try {
            $scheduler = $this->schedulerRepository->getById($schedulerId);

            /** @var \MageSuite\ErpConnector\Model\ProviderProcessor\ProviderProcessorInterface $providerProcessor */
            $providerProcessor = $this->getProviderProcessor($scheduler);

            if ($providerProcessor) {
                $providerProcessor->setScheduler($scheduler);
                $providerProcessor->execute();
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
    }

    public function getProviderProcessor(\MageSuite\ErpConnector\Model\Data\Scheduler $scheduler)
    {
        try {
            $provider = $this->providerRepository->getById($scheduler->getProviderId());
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->logger->critical(sprintf('Can`t found provider with id: %s', $scheduler->getProviderId()));
            return null;
        }

        return $this->schedulersPool->getProviderProcessorBySchedulerTypeAndProviderCode($scheduler->getType(), $provider->getCode());
    }
}
