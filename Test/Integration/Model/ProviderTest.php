<?php

namespace MageSuite\ErpConnector\Test\Integration\Model;

class ProviderTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * @var \MageSuite\ErpConnector\Model\ProviderRepository
     */
    protected $providerRepository;

    /**
     * @var \MageSuite\ErpConnector\Model\ProviderAdditionalConfigurationRepository
     */
    protected $providerAdditionalConfigurationRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->providerRepository = $this->_objectManager->get(\MageSuite\ErpConnector\Model\ProviderRepository::class);
        $this->providerAdditionalConfigurationRepository = $this->_objectManager->get(\MageSuite\ErpConnector\Model\ProviderAdditionalConfigurationRepository::class);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ErpConnector::Test/Integration/_files/provider.php
     */
    public function testGetProviderByName()
    {
        $provider = $this->providerRepository->getByName('Test Provider');

        $this->assertEquals('Test Provider', $provider->getName());
        $this->assertEquals('test@example.com', $provider->getEmail());
        $this->assertEquals('test-provider', $provider->getCode());
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_ErpConnector::Test/Integration/_files/provider.php
     * @magentoConfigFixture default/erp_connector/general/provider_configuration_codes {"_1633442288631_631":{"label":"Address","value":"address"}}
     */
    public function testItSavesAndLoadAdditionalConfigurationCorrectly()
    {
        $provider = $this->providerRepository->getByName('Test Provider');

        $this->saveProviderWithAdditionalConfiguration($provider);

        $additionalConfiguration = $this->providerAdditionalConfigurationRepository->getByProviderId($provider->getId());
        $configurationItem = current($additionalConfiguration);

        $this->assertEquals($provider->getId(), $configurationItem->getProviderId());
        $this->assertEquals('address', $configurationItem->getKey());
        $this->assertEquals('Test Address', $configurationItem->getValue());

    }

    protected function saveProviderWithAdditionalConfiguration($provider)
    {
        $data = ['general' => $provider->getData()];
        $data['general']['additional_configuration']['additional_configuration'][] = [
            'key' => 'address',
            'value' => 'Test Address'
        ];

        $this->getRequest()->setMethod(\Magento\Framework\App\Request\Http::METHOD_POST);
        $this->getRequest()->setPostValue($data);
        $this->dispatch('backend/erp_connector/provider/save');
    }
}
