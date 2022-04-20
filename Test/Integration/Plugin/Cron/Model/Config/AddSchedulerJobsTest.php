<?php

namespace MageSuite\ErpConnector\Test\Integration\Plugin\Cron\Model\Config;

/**
 * @magentoAppArea adminhtml
 */
class AddSchedulerJobsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Cron\Model\Config
     */
    protected $cronConfig;

    /**
     * @var \MageSuite\ErpConnector\Api\ProviderRepositoryInterface
     */
    protected $providerRepository;

    /**
     * @var \MageSuite\ErpConnector\Api\SchedulerRepositoryInterface
     */
    protected $schedulerRepository;

    protected function setUp(): void
    {
        $objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->cronConfig = $objectManager->get(\Magento\Cron\Model\Config::class);
        $this->providerRepository = $objectManager->get(\MageSuite\ErpConnector\Api\ProviderRepositoryInterface::class);
        $this->schedulerRepository = $objectManager->get(\MageSuite\ErpConnector\Api\SchedulerRepositoryInterface::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ErpConnector::Test/Integration/_files/scheduler.php
     */
    public function testItAddsErpConnectorSchedulers()
    {
        $jobs = $this->cronConfig->getJobs();

        $this->assertArrayHasKey('erp_connector', $jobs);

        $erpConnectorGroup = $jobs['erp_connector'];

        $provider = $this->providerRepository->getByName('Test Provider');
        $schedulers = $this->schedulerRepository->getByProviderIdAndType($provider->getId(), 'test');

        $this->assertCount(1, $schedulers);
        $scheduler = current($schedulers);

        $schedulerJobCode = sprintf(\MageSuite\ErpConnector\Helper\Configuration::CRON_JOB_METHOD_FORMAT, $scheduler->getId());

        $this->assertArrayHasKey($schedulerJobCode, $erpConnectorGroup);
        $this->assertEquals($schedulerJobCode, $erpConnectorGroup[$schedulerJobCode]['name']);
        $this->assertEquals('MageSuite\ErpConnector\Cron\Process', $erpConnectorGroup[$schedulerJobCode]['instance']);
        $this->assertEquals($schedulerJobCode, $erpConnectorGroup[$schedulerJobCode]['method']);
        $this->assertEquals('10 03 * * *', $erpConnectorGroup[$schedulerJobCode]['schedule']);
    }
}
