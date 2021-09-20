<?php
namespace MageSuite\ErpConnector\Model\Source;

class Connectors implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \MageSuite\ErpConnector\Api\ConnectorRepositoryInterface
     */
    protected $connectorRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \MageSuite\ErpConnector\Api\ConnectorRepositoryInterface $connectorRepository,
        \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->registry = $registry;
        $this->connectorRepository = $connectorRepository;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    public function toOptionArray()
    {
        $criteria = $this->searchCriteriaBuilderFactory->create();

        $currentProvider = $this->getCurrentProvider();

        if ($currentProvider && $currentProvider->getId()) {
            $criteria->addFilter('provider_id', $currentProvider->getId());
        }

        $connectors = $this->connectorRepository->getList($criteria->create());

        if (!$connectors->getTotalCount()) {
            return [];
        }

        $list = [
            ['value' => '-', 'label' => '-']
        ];

        foreach ($connectors->getItems() as $connector) {
            $list[] = [
                'value' => $connector->getId(),
                'label' => $connector->getName()
            ];
        }

        return $list;
    }

    private function getCurrentProvider()
    {
        return $this->registry->registry('current_provider');
    }
}
