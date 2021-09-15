<?php
namespace MageSuite\ErpConnector\Model\Data;

class SchedulerConnectorConfiguration extends \Magento\Framework\Model\AbstractModel
{
    const ID = 'id';
    const SCHEDULER_ID = 'scheduler_id';
    const PROVIDER_ID = 'provider_id';
    const CONNECTOR_ID = 'connector_id';

    const CACHE_TAG = 'erp_connector_scheduler_connector_configuration';
    const EVENT_PREFIX = 'erp_connector_scheduler_connector_configuration';

    protected $_cacheTag = self::CACHE_TAG; //phpcs:ignore
    protected $_eventPrefix = self::EVENT_PREFIX; //phpcs:ignore

    protected function _construct()
    {
        $this->_init(\MageSuite\ErpConnector\Model\ResourceModel\SchedulerConnectorConfiguration::class);
    }

    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function getSchedulerId()
    {
        return $this->getData(self::SCHEDULER_ID);
    }

    public function getProviderId()
    {
        return $this->getData(self::PROVIDER_ID);
    }

    public function getConnectorId()
    {
        return $this->getData(self::CONNECTOR_ID);
    }

    public function setId($id)
    {
        $this->setData(self::ID, $id);
        return $this;
    }

    public function setSchedulerId($schedulerId)
    {
        $this->setData(self::SCHEDULER_ID, $schedulerId);
        return $this;
    }

    public function setProviderId($providerId)
    {
        $this->setData(self::PROVIDER_ID, $providerId);
        return $this;
    }

    public function setConnectorId($connectorId)
    {
        $this->setData(self::CONNECTOR_ID, $connectorId);
        return $this;
    }
}
