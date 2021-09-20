<?php
namespace MageSuite\ErpConnector\Ui\DataProvider\Listing;

class Provider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var \MageSuite\ErpConnector\Api\ProviderRepositoryInterface
     */
    protected $providerRepository;

    /**
     * @var \Magento\Ui\DataProvider\SearchResultFactory
     */
    protected $searchResultFactory;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Api\Search\ReportingInterface $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \MageSuite\ErpConnector\Api\ProviderRepositoryInterface $providerRepository,
        \Magento\Ui\DataProvider\SearchResultFactory $searchResultFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $reporting, $searchCriteriaBuilder, $request, $filterBuilder, $meta, $data);

        $this->providerRepository = $providerRepository;
        $this->searchResultFactory = $searchResultFactory;
    }

    public function getSearchResult()
    {
        $searchCriteria = $this->getSearchCriteria();
        $result = $this->providerRepository->getList($searchCriteria);

        return $this->searchResultFactory->create(
            $result->getItems(),
            $result->getTotalCount(),
            $searchCriteria,
            'id'
        );
    }
}
