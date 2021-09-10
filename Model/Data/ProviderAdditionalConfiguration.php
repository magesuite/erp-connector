<?php
namespace MageSuite\ErpConnector\Model\Data;

class ProviderAdditionalConfiguration extends \Magento\Framework\Model\AbstractModel
{
    const ID = 'id';
    const PROVIDER_ID = 'provider_id';
    const KEY = 'key';
    const VALUE = 'value';

    const CACHE_TAG = 'erp_connector_provider_additional_configuration';
    const EVENT_PREFIX = 'erp_connector_provider_additional_configuration';

    protected $_cacheTag = self::CACHE_TAG; //phpcs:ignore
    protected $_eventPrefix = self::EVENT_PREFIX; //phpcs:ignore

    protected function _construct()
    {
        parent::_construct();
        $this->_init(\MageSuite\ErpConnector\Model\ResourceModel\ProviderAdditionalConfiguration::class);
    }

    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    public function getProviderId()
    {
        return (int) $this->getData(self::PROVIDER_ID);
    }

    public function setProviderId($id)
    {
        return $this->setData(self::PROVIDER_ID, $id);
    }

    public function getKey()
    {
        return $this->getData(self::KEY);
    }

    public function setKey($key)
    {
        return $this->setData(self::KEY, $key);
    }

    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }
}
