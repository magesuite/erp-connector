<?php

namespace MageSuite\ErpConnector\Helper;

class Configuration
{
    const XML_PATH_IS_ENABLED = 'erp_connector/general/is_enabled';
    const XML_PATH_PROVIDER_CONFIGURATION_CODES = 'erp_connector/general/provider_configuration_codes';
    const XML_PATH_EMAIL_GENERAL_NAME = 'trans_email/ident_general/name';
    const XML_PATH_EMAIL_GENERAL_EMAIL = 'trans_email/ident_general/email';
    const XML_PATH_CONNECTOR_SFTP_PROXY_HOST = 'erp_connector/connector/sftp_proxy_host';
    const XML_PATH_CONNECTOR_SFTP_PROXY_PORT = 'erp_connector/connector/sftp_proxy_port';
    const XML_PATH_CONNECTOR_HTTP_PROXY = 'erp_connector/connector/http_proxy';
    const XML_PATH_CONNECTOR_SOAP_PROXY_HOST = 'erp_connector/connector/soap_proxy_host';
    const XML_PATH_CONNECTOR_SOAP_PROXY_PORT = 'erp_connector/connector/soap_proxy_port';

    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    protected \Magento\Framework\Serialize\SerializerInterface $serializer;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
    }

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_IS_ENABLED);
    }

    public function getProviderAdditionalConfigurationCodes(): array
    {
        $configurationCodes = $this->scopeConfig->getValue(self::XML_PATH_PROVIDER_CONFIGURATION_CODES);

        if (empty($configurationCodes)) {
            return [];
        }

        return $this->serializer->unserialize($configurationCodes);
    }

    public function getSftpConnectorProxy(): array
    {
        return [
            'host' => (string)$this->scopeConfig->getValue(self::XML_PATH_CONNECTOR_SFTP_PROXY_HOST),
            'port' => (string)$this->scopeConfig->getValue(self::XML_PATH_CONNECTOR_SFTP_PROXY_PORT)
        ];
    }

    public function getHttpConnectorProxy(): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_CONNECTOR_HTTP_PROXY);
    }

    public function getSoapConnectorProxyHost(): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_CONNECTOR_SOAP_PROXY_HOST);
    }

    public function getSoapConnectorProxyPort(): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_CONNECTOR_SOAP_PROXY_PORT);
    }

    public function getEmailSenderInfo(): array
    {
        return [
            'name' => $this->scopeConfig->getValue(self::XML_PATH_EMAIL_GENERAL_NAME, \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'email' => $this->scopeConfig->getValue(self::XML_PATH_EMAIL_GENERAL_EMAIL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE)
        ];
    }
}
