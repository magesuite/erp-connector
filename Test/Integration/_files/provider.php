<?php

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$providerRepository = $objectManager->create(\MageSuite\ErpConnector\Api\ProviderRepositoryInterface::class);

$provider = $objectManager->create(\MageSuite\ErpConnector\Model\Data\Provider::class);
$provider->isObjectNew(true);
$provider
    ->setName('Test Provider')
    ->setEmail('test@example.com')
    ->setCode('test-provider');

$providerRepository->save($provider);
