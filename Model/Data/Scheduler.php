<?php
namespace MageSuite\ErpConnector\Model\Data;

class Scheduler extends \Magento\Framework\Model\AbstractModel implements \MageSuite\ErpConnector\Api\Data\SchedulerInterface
{
    protected $_cacheTag = self::CACHE_TAG; //phpcs:ignore
    protected $_eventPrefix = self::EVENT_PREFIX; //phpcs:ignore

    protected function _construct()
    {
        $this->_init(\MageSuite\ErpConnector\Model\ResourceModel\Scheduler::class);
    }

    public function getId()
    {
        return $this->getData(self::SCHEDULER_ID);
    }

    public function getProviderId()
    {
        return $this->getData(self::PROVIDER_ID);
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function getCronExpression()
    {
        return $this->getData(self::CRON_EXPRESSION);
    }

    public function getTemplate()
    {
        return $this->getData(self::TEMPLATE);
    }

    public function getFileName()
    {
        return $this->getData(self::FILE_NAME);
    }

    public function getAdditionalTemplate()
    {
        return $this->getData(self::ADDITIONAL_TEMPLATE);
    }

    public function setId($id)
    {
        $this->setData(self::SCHEDULER_ID, $id);
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

    public function setCronExpression($cronExpression)
    {
        $this->setData(self::CRON_EXPRESSION, $cronExpression);
        return $this;
    }

    public function setTemplate($template)
    {
        $this->setData(self::TEMPLATE, $template);
        return $this;
    }

    public function setFileName($fileName)
    {
        $this->setData(self::FILE_NAME, $fileName);
        return $this;
    }

    public function setAdditionalTemplate($template)
    {
        $this->setData(self::ADDITIONAL_TEMPLATE, $template);
        return $this;
    }
}
