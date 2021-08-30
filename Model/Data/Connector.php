<?php
namespace MageSuite\ErpConnector\Model\Data;

class Connector extends \Magento\Framework\Model\AbstractModel implements \MageSuite\ErpConnector\Api\Data\ConnectorInterface
{
    protected $_cacheTag = self::CACHE_TAG; //phpcs:ignore
    protected $_eventPrefix = self::EVENT_PREFIX; //phpcs:ignore

    protected function _construct()
    {
        $this->_init(\MageSuite\ErpConnector\Model\ResourceModel\Connector::class);
    }

    public function getId()
    {
        return $this->getData(self::CONNECTOR_ID);
    }

    public function getConnectorId()
    {
        return $this->getData(self::CONNECTOR_ID);
    }

    public function getProviderId()
    {
        return $this->getData(self::PROVIDER_ID);
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    public function setConnectorId($id)
    {
        $this->setData(self::CONNECTOR_ID, $id);
        return $this;
    }

    public function setProviderId($id)
    {
        $this->setData(self::PROVIDER_ID, $id);
        return $this;
    }

    public function setName($name)
    {
        $this->setData(self::NAME, $name);
        return $this;
    }

    public function setType($type)
    {
        $this->setData(self::TYPE, $type);
        return $this;
    }
}
