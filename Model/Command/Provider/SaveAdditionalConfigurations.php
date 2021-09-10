<?php
namespace MageSuite\ErpConnector\Model\Command\Provider;

class SaveAdditionalConfigurations
{
    /**
     * @var \MageSuite\ErpConnector\Model\Data\ProviderAdditionalConfigurationFactory
     */
    protected $providerAdditionalConfigurationFactory;

    /**
     * @var \MageSuite\ErpConnector\Api\ProviderAdditionalConfigurationRepositoryInterface
     */
    protected $providerAdditionalConfigurationRepository;

    public function __construct(
        \MageSuite\ErpConnector\Model\Data\ProviderAdditionalConfigurationFactory $providerAdditionalConfigurationFactory,
        \MageSuite\ErpConnector\Api\ProviderAdditionalConfigurationRepositoryInterface $providerAdditionalConfigurationRepository
    ) {
        $this->providerAdditionalConfigurationFactory = $providerAdditionalConfigurationFactory;
        $this->providerAdditionalConfigurationRepository = $providerAdditionalConfigurationRepository;
    }

    public function execute($providerId, $formData)
    {
        $providerAdditionalConfiguration = $this->providerAdditionalConfigurationRepository->getByProviderId($providerId);

        $configsData = [];

        if (!empty($formData)) {
            foreach ($formData as $configData) {
                $configData['provider_id'] = $providerId;

                if (isset($configData['id'])) {
                    $configsData[$configData['id']] = $configData;
                } else {
                    $config = $this->providerAdditionalConfigurationFactory->create();
                    $config->setData($configData);
                    $this->providerAdditionalConfigurationRepository->save($config);
                }
            }
        }

        foreach ($providerAdditionalConfiguration as $config) {

            if (isset($configsData[$config->getId()])) {
                $config->setData($configsData[$config->getId()]);
                $this->providerAdditionalConfigurationRepository->save($config);
            } else {
                $this->providerAdditionalConfigurationRepository->delete($config);
            }
        }
    }
}
