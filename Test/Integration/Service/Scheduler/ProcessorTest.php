<?php

namespace MageSuite\ErpConnector\Test\Integration\Service\Scheduler;

/**
 * @magentoAppArea adminhtml
 */
class ProcessorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \MageSuite\ErpConnector\Api\ProviderRepositoryInterface
     */
    protected $providerRepository;

    /**
     * @var \MageSuite\ErpConnector\Api\SchedulerRepositoryInterface
     */
    protected $schedulerRepository;

    /**
     * @var \MageSuite\ErpConnector\Service\Scheduler\Processor
     */
    protected $schedulerProcessor;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->providerRepository = $objectManager->get(\MageSuite\ErpConnector\Api\ProviderRepositoryInterface::class);
        $this->schedulerRepository = $objectManager->get(\MageSuite\ErpConnector\Api\SchedulerRepositoryInterface::class);
        $this->schedulerProcessor = $objectManager->get(\MageSuite\ErpConnector\Service\Scheduler\Processor::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ErpConnector::Test/Integration/_files/scheduler.php
     */
    public function testItReturnsProviderProcessor()
    {
        $provider = $this->providerRepository->getByName('Test Provider');
        $schedulers = $this->schedulerRepository->getByProviderIdAndType($provider->getId(), 'test');
        $scheduler = current($schedulers);

        $providerProcessor = $this->schedulerProcessor->getProviderProcessor($scheduler);

        $this->assertInstanceOf(\MageSuite\ErpConnector\Model\ProviderProcessor\General::class, $providerProcessor);

    }
}
