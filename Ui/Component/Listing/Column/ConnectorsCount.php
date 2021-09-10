<?php
namespace MageSuite\ErpConnector\Ui\Component\Listing\Column;

class ConnectorsCount extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \MageSuite\ErpConnector\Api\ConnectorRepositoryInterface
     */
    protected $connectorRepository;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \MageSuite\ErpConnector\Api\ConnectorRepositoryInterface $connectorRepository,
        array $components = [],
        array $data = []
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->connectorRepository = $connectorRepository;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        $fieldName = $this->getData('name');
        $connectorsCount = $this->getConnectorsCountGroupedByProviderId();

        foreach ($dataSource['data']['items'] as &$item) {
            if (!isset($item['id'])) {
                $item[$fieldName] = '';
                continue;
            }

            $item[$fieldName] = $connectorsCount[$item['id']] ?? 0;
        }

        return $dataSource;
    }

    protected function getConnectorsCountGroupedByProviderId()
    {
        $connectors = $this->connectorRepository->getList($this->searchCriteriaBuilder->create());

        $result = [];

        foreach ($connectors->getItems() as $connector) {
            $providerId = $connector->getProviderId();

            if (!isset($result[$providerId])) {
                $result[$providerId] = 0;
            }

            $result[$providerId]++;
        }

        return $result;
    }
}
