<?php
$resolver = \Magento\TestFramework\Workaround\Override\Fixture\Resolver::getInstance();
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$schedulerRepository = $objectManager->get(\MageSuite\ErpConnector\Api\SchedulerRepositoryInterface::class);

$resolver->requireDataFixture('MageSuite_ErpConnector::Test/Integration/_files/provider_rollback.php');

foreach ($schedulerRepository->getList() as $scheduler) {
    $schedulerRepository->delete($scheduler);
}
