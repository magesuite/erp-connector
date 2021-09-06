<?php
namespace MageSuite\ErpConnector\Model;

class ProviderRepository implements \MageSuite\ErpConnector\Api\ProviderRepositoryInterface
{
    /**
     * @var array loaded providers
     */
    protected $providers = [];

    /**
     * @var \MageSuite\ErpConnector\Model\ResourceModel\Provider
     */
    protected $resourceModel;

    /**
     * @var \MageSuite\ErpConnector\Api\Data\ProviderInterfaceFactory
     */
    protected $providerFactory;

    /**
     * @var \MageSuite\ErpConnector\Model\ResourceModel\Provider\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \MageSuite\ErpConnector\Api\Data\ProviderSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    protected $collectionProcessor;

    public function __construct(
        \MageSuite\ErpConnector\Model\ResourceModel\Provider $resourceModel,
        \MageSuite\ErpConnector\Api\Data\ProviderInterfaceFactory $providerFactory,
        \MageSuite\ErpConnector\Model\ResourceModel\Provider\CollectionFactory $collectionFactory,
        \MageSuite\ErpConnector\Api\Data\ProviderSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resourceModel = $resourceModel;
        $this->providerFactory = $providerFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function save(\MageSuite\ErpConnector\Api\Data\ProviderInterface $provider)
    {
        try {
            $this->resourceModel->save($provider);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__($exception->getMessage()));
        }

        return $provider;
    }

    public function getById(int $id)
    {
        if (isset($this->providers[$id])) {
            return $this->providers[$id];
        }

        $provider = $this->providerFactory->create();
        $this->resourceModel->load($provider, $id);

        if (!$provider->getProviderId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('The provider with the "%1" ID doesn\'t exist.', $id));
        }

        $this->providers[$id] = $provider;

        return $this->providers[$id];
    }

    public function getByName($name)
    {
        $provider = $this->providerFactory->create();
        $this->resourceModel->load($provider, $name, 'name');

        if (!$provider->getProviderId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('The provider with the name "%1" doesn\'t exist.', $name));
        }

        return $provider;
    }

    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria = null)
    {
        $collection = $this->collectionFactory->create();

        if ($criteria === null) {
            $criteria = $this->searchCriteriaBuilder->create();
        } else {
            $this->collectionProcessor->process($criteria, $collection);
        }

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    public function delete(\MageSuite\ErpConnector\Api\Data\ProviderInterface $provider)
    {
        try {
            $this->resourceModel->delete($provider);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    public function deleteById(int $id): bool
    {
        return $this->delete($this->getById($id));
    }
}
