<?php
namespace MageSuite\ErpConnector\Model\Data;

class ProviderAdditionalConfiguration extends \Magento\Framework\Model\AbstractModel implements \MageSuite\ErpConnector\Api\Data\ProviderAdditionalConfigurationInterface
{
    protected $_cacheTag = self::CACHE_TAG; //phpcs:ignore
    protected $_eventPrefix = self::EVENT_PREFIX; //phpcs:ignore

    protected function _construct()
    {
        parent::_construct();
        $this->_init(\MageSuite\ErpConnector\Model\ResourceModel\ProviderAdditionalConfiguration::class);
    }

    public function getProviderId()
    {
        return (int) $this->_getData(self::PROVIDER_ID);
    }

    public function setProviderId($id)
    {
        return $this->setData(self::PROVIDER_ID, $id);
    }

    public function getKey()
    {
        return $this->_getData(self::KEY);
    }

    public function setKey($key)
    {
        return $this->setData(self::KEY, $key);
    }

    public function getValue()
    {
        return $this->_getData(self::VALUE);
    }

    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }
}
