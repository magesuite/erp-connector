<?php
namespace MageSuite\ErpConnector\Model\Data;

class Connector extends \Magento\Framework\Model\AbstractModel
{
    const ID = 'id';
    const PROVIDER_ID = 'provider_id';
    const NAME = 'name';
    const TYPE = 'type';

    const CACHE_TAG = 'erp_connector';
    const EVENT_PREFIX = 'erp_connector';

    protected $_cacheTag = self::CACHE_TAG; //phpcs:ignore
    protected $_eventPrefix = self::EVENT_PREFIX; //phpcs:ignore

    protected function _construct()
    {
        $this->_init(\MageSuite\ErpConnector\Model\ResourceModel\Connector::class);
    }

    public function getId()
    {
        return $this->getData(self::ID);
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
