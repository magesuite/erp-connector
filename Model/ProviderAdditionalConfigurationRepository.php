<?php
namespace MageSuite\ErpConnector\Model;

class ProviderAdditionalConfigurationRepository implements \MageSuite\ErpConnector\Api\ProviderAdditionalConfigurationRepositoryInterface
{
    /**
     * @var \MageSuite\ErpConnector\Model\ResourceModel\ProviderAdditionalConfiguration
     */
    protected $resourceModel;

    /**
     * @var \MageSuite\ErpConnector\Api\Data\ProviderAdditionalConfigurationInterfaceFactory
     */
    protected $providerAdditionalConfigurationFactory;

    /**
     * @var \MageSuite\ErpConnector\Model\ResourceModel\ProviderAdditionalConfiguration\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \MageSuite\ErpConnector\Api\Data\ProviderAdditionalConfigurationSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    public function __construct(
        \MageSuite\ErpConnector\Model\ResourceModel\ProviderAdditionalConfiguration $resourceModel,
        \MageSuite\ErpConnector\Api\Data\ProviderAdditionalConfigurationInterfaceFactory $providerAdditionalConfigurationFactory,
        \MageSuite\ErpConnector\Model\ResourceModel\ProviderAdditionalConfiguration\CollectionFactory $collectionFactory,
        \MageSuite\ErpConnector\Api\Data\ProviderAdditionalConfigurationSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
    ) {
        $this->resourceModel = $resourceModel;
        $this->providerAdditionalConfigurationFactory = $providerAdditionalConfigurationFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
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

        return $providerAdditionalConfiguration->getDataModel(); //TODO: to poprawne?
    }

    public function getByProviderId($id)
    {
        $providerAdditionalConfiguration = $this->providerAdditionalConfigurationFactory->create();
        $this->resourceModel->load($providerAdditionalConfiguration, $id, 'provider_id');

        if (!$providerAdditionalConfiguration->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('The configuration with the "%1" Provider ID doesn\'t exist.', $id));
        }

        return $providerAdditionalConfiguration;
    }

    public function getCollectionByProviderId($id)
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('provider_id', $id);

        return $collection;
    }

    public function getList($criteria)
    {
        $collection = $this->collectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \MageSuite\ErpConnector\Api\Data\ProviderAdditionalConfigurationInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];

        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
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
