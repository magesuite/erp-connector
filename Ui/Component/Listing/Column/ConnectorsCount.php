<?php
namespace MageSuite\ErpConnector\Ui\Component\Listing\Column;

class ConnectorsCount extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilderFactory
     */
    protected $searchCriteriaBuilderFactory;

    /**
     * @var \MageSuite\ErpConnector\Api\ConnectorRepositoryInterface
     */
    protected $connectorRepository;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\Api\SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        \MageSuite\ErpConnector\Api\ConnectorRepositoryInterface $connectorRepository,
        array $components = [],
        array $data = []
    ) {
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->connectorRepository = $connectorRepository;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        $fieldName = $this->getData('name');

        foreach ($dataSource['data']['items'] as &$item) {
            if (!isset($item['provider_id'])) {
                $item[$fieldName] = '';
                continue;
            }

            $criteria = $this->searchCriteriaBuilderFactory
                ->create()
                ->addFilter('provider_id', $item['provider_id']);

            $connectors = $this->connectorRepository->getList($criteria->create());
            $item[$fieldName] = $connectors->getTotalCount();
        }

        return $dataSource;
    }
}
