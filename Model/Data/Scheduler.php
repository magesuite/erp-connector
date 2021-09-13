<?php
namespace MageSuite\ErpConnector\Model\Data;

class Scheduler extends \Magento\Framework\Model\AbstractModel
{
    const ID = 'id';
    const PROVIDER_ID = 'provider_id';
    const CONNECTOR_ID = 'connector_id';
    const NAME = 'name';
    const TYPE = 'type';
    const CRON_EXPRESSION = 'cron_expression';
    const TEMPLATES = 'templates';
    const FILE_NAME = 'file_name';

    const CACHE_TAG = 'erp_connector_scheduler';
    const EVENT_PREFIX = 'erp_connector_scheduler';

    protected $_cacheTag = self::CACHE_TAG; //phpcs:ignore
    protected $_eventPrefix = self::EVENT_PREFIX; //phpcs:ignore

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->serializer = $serializer;
    }

    protected function _construct()
    {
        $this->_init(\MageSuite\ErpConnector\Model\ResourceModel\Scheduler::class);
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

    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    public function getCronExpression()
    {
        return $this->getData(self::CRON_EXPRESSION);
    }

    public function getTemplates()
    {
        $templates = $this->getData(self::TEMPLATES);

        if (empty($templates)) {
            return $templates;
        }

        return $this->serializer->unserialize($templates);
    }

    public function getFileName()
    {
        return $this->getData(self::FILE_NAME);
    }

    public function setId($id)
    {
        $this->setData(self::ID, $id);
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

    public function setCronExpression($cronExpression)
    {
        $this->setData(self::CRON_EXPRESSION, $cronExpression);
        return $this;
    }

    public function setTemplates($templates)
    {
        if (empty($templates)) {
            $this->setData(self::TEMPLATES, $templates);
            return $this;
        }

        $serializedTemplates = $this->serializer->serialize($templates);

        $this->setData(self::TEMPLATES, $serializedTemplates);
        return $this;
    }

    public function setFileName($fileName)
    {
        $this->setData(self::FILE_NAME, $fileName);
        return $this;
    }
}
