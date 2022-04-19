<?php

$resolver = \Magento\TestFramework\Workaround\Override\Fixture\Resolver::getInstance();
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$providerRepository = $objectManager->get(\MageSuite\ErpConnector\Api\ProviderRepositoryInterface::class);
$schedulerRepository = $objectManager->get(\MageSuite\ErpConnector\Api\SchedulerRepositoryInterface::class);

$resolver->requireDataFixture('MageSuite_ErpConnector::Test/Integration/_files/provider.php');

$provider = $providerRepository->getByName('Test Provider');

$scheduler = $objectManager->create(\MageSuite\ErpConnector\Model\Data\Scheduler::class);
$scheduler->isObjectNew(true);
$scheduler
    ->setProviderId($provider->getId())
    ->setName('Test Scheduler')
    ->setType('test')
    ->setCronExpression('10 03 * * *');

$schedulerRepository->save($scheduler);
