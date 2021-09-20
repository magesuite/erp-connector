<?php
namespace MageSuite\ErpConnector\Model;

class ProviderAdditionalConfigurationRepository implements \MageSuite\ErpConnector\Api\ProviderAdditionalConfigurationRepositoryInterface
{
    /**
     * @var \MageSuite\ErpConnector\Model\ResourceModel\ProviderAdditionalConfiguration
     */
    protected $resourceModel;

    /**
     * @var \MageSuite\ErpConnector\Model\Data\ProviderAdditionalConfigurationFactory
     */
    protected $providerAdditionalConfigurationFactory;

    /**
     * @var \MageSuite\ErpConnector\Model\ResourceModel\ProviderAdditionalConfiguration\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Api\SearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    public function __construct(
        \MageSuite\ErpConnector\Model\ResourceModel\ProviderAdditionalConfiguration $resourceModel,
        \MageSuite\ErpConnector\Model\Data\ProviderAdditionalConfigurationFactory $providerAdditionalConfigurationFactory,
        \MageSuite\ErpConnector\Model\ResourceModel\ProviderAdditionalConfiguration\CollectionFactory $collectionFactory,
        \Magento\Framework\Api\SearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->resourceModel = $resourceModel;
        $this->providerAdditionalConfigurationFactory = $providerAdditionalConfigurationFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function save($providerAdditionalConfiguration)
    {
        try {
            $this->resourceModel->save($providerAdditionalConfiguration);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__($exception->getMessage()));
        }

        return $providerAdditionalConfiguration;
    }

    public function getById($id)
    {
        $providerAdditionalConfiguration = $this->providerAdditionalConfigurationFactory->create();
        $this->resourceModel->load($providerAdditionalConfiguration, $id);

        if (!$providerAdditionalConfiguration->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('ProviderAdditional with id "%1" does not exist.', $id));
        }

        return $providerAdditionalConfiguration->getDataModel();
    }

    public function getByProviderId($providerId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(\MageSuite\ErpConnector\Model\Data\ProviderAdditionalConfiguration::PROVIDER_ID, $providerId)
            ->create();

        $list = $this->getList($searchCriteria);

        if (!$list->getTotalCount()) {
            return [];
        }

        return $list->getItems();
    }

    public function getList($criteria)
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

    public function delete($providerAdditional)
    {
        try {
            $providerAdditionalModelConfiguration = $this->providerAdditionalConfigurationFactory->create();
            $this->resourceModel->load($providerAdditionalModelConfiguration, $providerAdditional->getId());

            $this->resourceModel->delete($providerAdditionalModelConfiguration);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__(
                'Could not delete the ProviderAdditional: %1',
                $exception->getMessage()
            ));
        }

        return true;
    }

    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
