<?php
namespace MageSuite\ErpConnector\Helper;

class Configuration
{
    const XML_PATH_IS_ENABLED = 'erp_connector/general/is_enabled';
    const XML_PATH_PROVIDER_CONFIGURATION_CODES = 'erp_connector/general/provider_configuration_codes';
    const XML_PATH_EMAIL_GENERAL_NAME = 'trans_email/ident_general/name';
    const XML_PATH_EMAIL_GENERAL_EMAIL = 'trans_email/ident_general/email';
    const XML_PATH_CONNECTOR_HTTP_PROXY = 'erp_connector/connector/http_proxy';

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
            return [];
        }

        return $this->serializer->unserialize($configurationCodes);
    }

    public function getHttpConnectorProxy()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CONNECTOR_HTTP_PROXY);
    }

    public function getEmailSenderInfo()
    {
        return [
            'name' => $this->scopeConfig->getValue(self::XML_PATH_EMAIL_GENERAL_NAME, \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'email' => $this->scopeConfig->getValue(self::XML_PATH_EMAIL_GENERAL_EMAIL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
        ];
    }
}
