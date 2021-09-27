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

    /**
     * @var \MageSuite\ErpConnector\Model\ConnectorResolver
     */
    protected $connectorResolver;

    /**
     * @var \MageSuite\ErpConnector\Api\ConnectorConfigurationRepositoryInterface
     */
    protected $connectorConfigurationRepository;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \MageSuite\ErpConnector\Model\ConnectorResolver $connectorResolver,
        \MageSuite\ErpConnector\Api\ConnectorConfigurationRepositoryInterface $connectorConfigurationRepository,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->connectorResolver = $connectorResolver;
        $this->connectorConfigurationRepository = $connectorConfigurationRepository;
    }

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

    public function getClient()
    {
        $client = $this->connectorResolver->getClient($this->getType());

        $connectorConfiguration = $this->connectorConfigurationRepository->getByConnectorId($this->getId());

        foreach ($connectorConfiguration as $item) {
            $client->setData($item->getKey(), $item->getValue());
        }

        return $client;
    }
}
