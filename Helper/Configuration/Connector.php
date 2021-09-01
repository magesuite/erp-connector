<?php
namespace MageSuite\ErpConnector\Helper\Configuration;

class Connector
{
    const XML_PATH_CONNECTOR_CONFIGURATION = 'erp_connector/connector';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $config = null;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface)
    {
        $this->scopeConfig = $scopeConfigInterface;
    }

    public function getEmailSenderData()
    {
        return [
            'name' => $this->getConfig()->getEmail()['name'],
            'email' => $this->getConfig()->getEmail()['email'],
        ];
    }

    protected function getConfig()
    {
        if ($this->config === null) {
            $config = $this->scopeConfig->getValue(self::XML_PATH_CONNECTOR_CONFIGURATION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

            $this->config = new \Magento\Framework\DataObject($config ?? []);
        }

        return $this->config;
    }
}
