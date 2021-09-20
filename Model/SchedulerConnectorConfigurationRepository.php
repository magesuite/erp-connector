<?php
namespace MageSuite\ErpConnector\Model;

class SchedulerConnectorConfigurationRepository implements \MageSuite\ErpConnector\Api\SchedulerConnectorConfigurationRepositoryInterface
{
    protected $schedulerConnectorConfigurations = [];

    /**
     * @var \MageSuite\ErpConnector\Model\ResourceModel\SchedulerConnectorConfiguration
     */
    protected $resourceModel;

    /**
     * @var \MageSuite\ErpConnector\Model\Data\SchedulerConnectorConfigurationFactory
     */
    protected $schedulerConnectorConfigurationFactory;

    /**
     * @var \MageSuite\ErpConnector\Model\ResourceModel\SchedulerConnectorConfiguration\CollectionFactory
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
        \MageSuite\ErpConnector\Model\ResourceModel\SchedulerConnectorConfiguration $resourceModel,
        \MageSuite\ErpConnector\Model\Data\SchedulerConnectorConfigurationFactory $schedulerConnectorConfigurationFactory,
        \MageSuite\ErpConnector\Model\ResourceModel\SchedulerConnectorConfiguration\CollectionFactory $collectionFactory,
        \Magento\Framework\Api\SearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->resourceModel = $resourceModel;
        $this->schedulerConnectorConfigurationFactory = $schedulerConnectorConfigurationFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function save($schedulerConnectorConfiguration)
    {
        try {
            $this->resourceModel->save($schedulerConnectorConfiguration);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__($exception->getMessage()));
        }

        return $schedulerConnectorConfiguration;
    }

    public function getById($id)
    {
        if (isset($this->schedulerConnectorConfigurations[$id])) {
            return $this->schedulerConnectorConfigurations[$id];
        }

        $schedulerConnectorConfiguration = $this->schedulerConnectorConfigurationFactory->create();
        $this->resourceModel->load($schedulerConnectorConfiguration, $id);

        if (!$schedulerConnectorConfiguration->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('The scheduler connector configuration with the "%1" ID doesn\'t exist.', $id));
        }

        $this->schedulerConnectorConfigurations[$id] = $schedulerConnectorConfiguration;

        return $this->schedulerConnectorConfigurations[$id];
    }

    public function getBySchedulerId($schedulerId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(\MageSuite\ErpConnector\Model\Data\SchedulerConnectorConfiguration::SCHEDULER_ID, $schedulerId)
            ->create();

        $list = $this->getList($searchCriteria);

        if (!$list->getTotalCount()) {
            return [];
        }

        return $list->getItems();
    }

    public function getByProviderId($providerId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(\MageSuite\ErpConnector\Model\Data\SchedulerConnectorConfiguration::PROVIDER_ID, $providerId)
            ->create();

        $list = $this->getList($searchCriteria);

        if (!$list->getTotalCount()) {
            return [];
        }

        return $list->getItems();
    }

    public function getList($criteria = null)
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

    public function delete($scheduler)
    {
        try {
            $this->resourceModel->delete($scheduler);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }
}
