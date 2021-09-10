<?php
namespace MageSuite\ErpConnector\Model\Data;

class ConnectorConfiguration extends \Magento\Framework\Model\AbstractModel
{
    const ID = 'id';
    const PROVIDER_ID = 'provider_id';
    const CONNECTOR_ID = 'connector_id';
    const NAME = 'name';
    const VALUE = 'value';

    const CACHE_TAG = 'erp_connector_configuration';
    const EVENT_PREFIX = 'erp_connector_configuration';

    protected $_cacheTag = self::CACHE_TAG; //phpcs:ignore
    protected $_eventPrefix = self::EVENT_PREFIX; //phpcs:ignore

    protected function _construct(): void
    {
        parent::_construct();
        $this->_init(\MageSuite\ErpConnector\Model\ResourceModel\ConnectorConfiguration::class);
    }

    public function getId()
    {
        return $this->getData(self::ID);
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
        $this->setData(self::ID, $id);
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
