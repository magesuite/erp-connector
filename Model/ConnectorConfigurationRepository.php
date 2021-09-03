<?php
namespace MageSuite\ErpConnector\Model;

class ConnectorConfigurationRepository implements \MageSuite\ErpConnector\Api\ConnectorConfigurationRepositoryInterface
{
    protected $connectorConfigurations = [];

    /**
     * @var \MageSuite\ErpConnector\Model\ResourceModel\ConnectorConfiguration
     */
    protected $resourceModel;

    /**
     * @var \MageSuite\ErpConnector\Api\Data\ConnectorConfigurationInterfaceFactory
     */
    protected $connectorConfigurationFactory;

    /**
     * @var \MageSuite\ErpConnector\Model\ResourceModel\ConnectorConfiguration\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \MageSuite\ErpConnector\Api\Data\ConnectorConfigurationSearchResultsInterfaceFactory
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
        \MageSuite\ErpConnector\Model\ResourceModel\ConnectorConfiguration $resourceModel,
        \MageSuite\ErpConnector\Api\Data\ConnectorConfigurationInterfaceFactory $connectorConfigurationFactory,
        \MageSuite\ErpConnector\Model\ResourceModel\ConnectorConfiguration\CollectionFactory $collectionFactory,
        \MageSuite\ErpConnector\Api\Data\ConnectorConfigurationSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resourceModel = $resourceModel;
        $this->connectorConfigurationFactory = $connectorConfigurationFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function save($connectorConfiguration)
    {
        try {
            $this->resourceModel->save($connectorConfiguration);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__($exception->getMessage()));
        }

        return $connectorConfiguration;
    }

    public function getById($id)
    {
        if (isset($this->connectorConfigurations[$id])) {
            return $this->connectorConfigurations[$id];
        }

        $connectorConfiguration = $this->factory->create();
        $this->resourceModel->load($connectorConfiguration, $id);

        if (!$connectorConfiguration->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('The connector with the "%1" ID doesn\'t exist.', $id));
        }

        $this->connectorConfigurations[$id] = $connectorConfiguration;

        return $this->connectorConfigurations[$id];
    }

    public function getList($criteria)
    {
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    public function getByProviderId($providerId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(\MageSuite\ErpConnector\Api\Data\ConnectorConfigurationInterface::PROVIDER_ID, $providerId)
            ->create();

        $list = $this->getList($searchCriteria);

        if (!$list->getTotalCount()) {
            return [];
        }

        return $list->getItems();
    }

    public function getByConnectorId($connectorId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(\MageSuite\ErpConnector\Api\Data\ConnectorConfigurationInterface::CONNECTOR_ID, $connectorId)
            ->create();

        $list = $this->getList($searchCriteria);

        if (!$list->getTotalCount()) {
            return [];
        }

        return $list->getItems();
    }

    public function getItemByConnectorIdAndName($connectorId, $name)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(\MageSuite\ErpConnector\Api\Data\ConnectorConfigurationInterface::CONNECTOR_ID, $connectorId)
            ->addFilter(\MageSuite\ErpConnector\Api\Data\ConnectorConfigurationInterface::NAME, $name)
            ->create();

        $searchCriteria
            ->setPageSize(1)
            ->setCurrentPage(1);

        $list = $this->getList($searchCriteria);

        if (!$list->getTotalCount()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('The additional config with connector ID "%1" and name "%2" doesn\'t exist.', $id, $name)
            );
        }

        return current($list->getItems());
    }

    public function delete($model)
    {
        try {
            $this->resourceModel->delete($model);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    public function deleteById($entityId)
    {
        return $this->delete($this->getById($entityId));
    }
}
