<?php
namespace MageSuite\ErpConnector\Model\Data;

class ConnectorConfiguration extends \Magento\Framework\Model\AbstractModel implements \MageSuite\ErpConnector\Api\Data\ConnectorConfigurationInterface
{
    protected $_cacheTag = self::CACHE_TAG; //phpcs:ignore
    protected $_eventPrefix = self::EVENT_PREFIX; //phpcs:ignore

    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(\MageSuite\ErpConnector\Model\ResourceModel\ConnectorConfiguration::class);
    }

    public function getId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    public function getProviderId()
    {
        return $this->getData(self::PROVIDER_ID);
    }

    public function getConnectorId()
    {
        return $this->getData(self::CONNECTOR_ID);
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function getValue()
    {
        return $this->getData(self::VALUE);
    }

    public function setId($id)
    {
        $this->setData(self::ENTITY_ID, $id);
        return $this;
    }

    public function setProviderId($id)
    {
        $this->setData(self::PROVIDER_ID, $id);
        return $this;
    }

    public function setConnectorId($id)
    {
        $this->setData(self::CONNECTOR_ID, $id);
        return $this;
    }

    public function setName($name)
    {
        $this->setData(self::NAME, $name);
        return $this;
    }

    public function setValue($type)
    {
        $this->setData(self::VALUE, $type);
        return $this;
    }
}
