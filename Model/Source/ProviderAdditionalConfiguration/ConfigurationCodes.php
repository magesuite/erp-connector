<?php
namespace MageSuite\ErpConnector\Model\Source\ProviderAdditionalConfiguration;

class ConfigurationCodes implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \MageSuite\ErpConnector\Helper\Configuration
     */
    protected $configuration;

    public function __construct(\MageSuite\ErpConnector\Helper\Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function toOptionArray()
    {
        $options = [
            ['value' => '', 'label' => __('-- Please Select --')]
        ];

        $additionalConfigurationCodes = $this->configuration->getProviderAdditionalConfigurationCodes();

        if (empty($additionalConfigurationCodes)) {
            return $options;
        }

        foreach ($additionalConfigurationCodes as $configurationCode) {
            $options[] = ['value' => $configurationCode['value'], 'label' => __($configurationCode['label'])];
        }

        return $options;
    }
}
