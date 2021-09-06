<?php
namespace MageSuite\ErpConnector\Model\Command\ProviderAdditionalConfiguration;

class ValidateConfigurationCodes
{
    const ERROR_MESSAGE = 'Additional configuration for "%s" key is set in "%s" provider. Please remove it from provider first.';

    /**
     * @var \MageSuite\ErpConnector\Helper\Configuration
     */
    protected $configuration;

    /**
     * @var \MageSuite\ErpConnector\Model\ResourceModel\ProviderAdditionalConfiguration
     */
    protected $resourceModel;

    public function __construct(
        \MageSuite\ErpConnector\Helper\Configuration $configuration,
        \MageSuite\ErpConnector\Model\ResourceModel\ProviderAdditionalConfiguration $resourceModel
    ) {
        $this->configuration = $configuration;
        $this->resourceModel = $resourceModel;
    }

    public function execute($configurationCodes)
    {
        $changedCodes = $this->getChangedCodes($configurationCodes);

        if (empty($changedCodes)) {
            return [];
        }

        $providerNames = $this->resourceModel->getProviderNamesWithSpecificAdditionalConfig($changedCodes);

        if (empty($providerNames)) {
            return [];
        }

        $errorMessages = [];

        foreach ($providerNames as $providerName) {
            $errorMessages[] = sprintf(self::ERROR_MESSAGE, $providerName['key'], $providerName['name']);
        }

        return $errorMessages;
    }

    protected function getChangedCodes($newConfigurationCodes)
    {
        $newCodes = [];

        foreach ($newConfigurationCodes as $newCode) {
            if (empty($newCode)) {
                continue;
            }

            $newCodes[$newCode['label']] = $newCode['value'];
        }

        $changedCodes = [];

        foreach ($this->configuration->getProviderAdditionalConfigurationCodes() as $currentCode) {
            if (empty($currentCode)) {
                continue;
            }

            if (!isset($newCodes[$currentCode['label']])) {
                //code was removed
                $changedCodes[] = $currentCode['value'];
            } elseif ($newCodes[$currentCode['label']] != $currentCode['value']) {
                //value was changed
                $changedCodes[] = $currentCode['value'];
            }
        }

        return $changedCodes;
    }
}
