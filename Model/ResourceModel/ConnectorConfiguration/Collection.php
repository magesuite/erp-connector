<?php
namespace MageSuite\ErpConnector\Model\ResourceModel\ConnectorConfiguration;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);

        $this->objectManager = $objectManager;
    }

    protected function _construct(): void
    {
        $this->_init(
            \MageSuite\ErpConnector\Model\Data\ConnectorConfiguration::class,
            \MageSuite\ErpConnector\Model\ResourceModel\ConnectorConfiguration::class
        );
    }

    public function _afterLoad()
    {
        foreach ($this->getItems() as $item) {

            $modifierClassString = $item->getModifierClass();

            if (empty($modifierClassString)) {
                continue;
            }

            $modifierClass = $this->objectManager->create($modifierClassString);

            $value = $modifierClass->afterLoad($item->getValue());
            $item->setValue($value);
        }

        return parent::_afterLoad();
    }
}
