<?php
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$providerRepository = $objectManager->create(\MageSuite\ErpConnector\Api\ProviderRepositoryInterface::class);

$providers = $providerRepository->getList();

foreach ($providers->getItems() as $provider) {
    try {
        $providerRepository->delete($provider);
    } catch (\Exception $e) {

    }
}
