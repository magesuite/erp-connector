<?php
namespace MageSuite\ErpConnector\Model\Data;

class Provider extends \Magento\Framework\Model\AbstractExtensibleModel implements \MageSuite\ErpConnector\Api\Data\ProviderInterface
{
    protected $_cacheTag = self::CACHE_TAG; //phpcs:ignore
    protected $_eventPrefix = self::EVENT_PREFIX; //phpcs:ignore

    protected function _construct()
    {
        $this->_init(\MageSuite\ErpConnector\Model\ResourceModel\Provider::class);
    }

    public function getProviderId()
    {
        return $this->getDataByKey(self::PROVIDER_ID);
    }

    public function getName()
    {
        return $this->getDataByKey(self::NAME);
    }

    public function getEmail()
    {
        return (string) $this->getDataByKey(self::EMAIL);
    }

    public function getCode()
    {
        return $this->getDataByKey(self::CODE);
    }

    public function setProviderId($providerId)
    {
        $this->setData(self::PROVIDER_ID, $providerId);
        return $this;
    }

    public function setName($name)
    {
        $this->setData(self::NAME, $name);
        return $this;
    }

    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    public function setCode($code)
    {
        $this->setData(self::CODE, $code);
        return $this;
    }

    public function getExtensionAttributes()
    {
        if (!$this->_getExtensionAttributes()) {
            $extensionAttributes = $this->extensionAttributesFactory->create(\MageSuite\ErpConnector\Api\Data\ProviderInterface::class);
            $this->setExtensionAttributes($extensionAttributes);
        }

        return $this->_getExtensionAttributes();
        ;
    }

    public function setExtensionAttributes(\MageSuite\ErpConnector\Api\Data\ProviderExtensionInterface $extensionAttributes)
    {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    public function getIdentities()
    {
        return [
            self::CACHE_TAG,
            self::CACHE_TAG . '_' . $this->getId(),
        ];
    }

    public function getCacheTags()
    {
        return $this->getIdentities();
    }
}
