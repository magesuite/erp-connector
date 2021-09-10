<?php
namespace MageSuite\ErpConnector\Service\Scheduler;

class Processor
{
    /**
     * @var \MageSuite\ErpConnector\Api\ProviderRepositoryInterface
     */
    protected $providerRepository;

    /**
     * @var \MageSuite\ErpConnector\Model\SchedulerPool
     */
    protected $schedulerPool;

    /**
     * @var \MageSuite\ErpConnector\Logger\Logger
     */
    protected $logger;

    public function __construct(
        \MageSuite\ErpConnector\Api\ProviderRepositoryInterface $providerRepository,
        \MageSuite\ErpConnector\Model\SchedulerPool $schedulerPool,
        \MageSuite\ErpConnector\Logger\Logger $logger
    ) {
        $this->providerRepository = $providerRepository;
        $this->schedulerPool = $schedulerPool;
        $this->logger = $logger;
    }

    public function execute(\MageSuite\ErpConnector\Model\Data\Scheduler $scheduler)
    {
        try {
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

    protected function getProviderProcessor(\MageSuite\ErpConnector\Model\Data\Scheduler $scheduler)
    {
        try {
            $provider = $this->providerRepository->getById($scheduler->getProviderId());
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->logger->critical(sprintf('Can`t found provider with id: %s', $scheduler->getProviderId()));
            return null;
        }

        return $this->schedulerPool->getProviderProcessorBySchedulerTypeAndProviderCode($scheduler->getType(), $provider->getCode());
    }
}
