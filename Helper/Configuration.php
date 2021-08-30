<?php
namespace MageSuite\ErpConnector\Helper;

class Configuration
{
    const XML_PATH_IS_ENABLED = 'erp_connector/general/enabled';
    const XML_PATH_PROVIDER_CONFIGURATION_CODES = 'erp_connector/general/provider_configuration_codes';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
    }

    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_IS_ENABLED);
    }

    public function getProviderAdditionalConfigurationCodes()
    {
        $configurationCodes = $this->scopeConfig->getValue(self::XML_PATH_PROVIDER_CONFIGURATION_CODES);

        if (empty($configurationCodes)) {
            return null;
        }

        return $this->serializer->unserialize($configurationCodes);
    }
}
