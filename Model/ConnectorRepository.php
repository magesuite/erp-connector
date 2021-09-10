<?php
namespace MageSuite\ErpConnector\Model;

class ConnectorRepository implements \MageSuite\ErpConnector\Api\ConnectorRepositoryInterface
{
    protected $connectors = [];

    /**
     * @var \MageSuite\ErpConnector\Model\ResourceModel\Connector
     */
    protected $resourceModel;

    /**
     * @var \MageSuite\ErpConnector\Model\Data\ConnectorFactory
     */
    protected $connectorFactory;

    /**
     * @var \MageSuite\ErpConnector\Model\ResourceModel\Connector\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Framework\Api\SearchResultsInterfaceFactory
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
        \MageSuite\ErpConnector\Model\ResourceModel\Connector $resourceModel,
        \MageSuite\ErpConnector\Model\Data\ConnectorFactory $connectorFactory,
        \MageSuite\ErpConnector\Model\ResourceModel\Connector\CollectionFactory $collectionFactory,
        \Magento\Framework\Api\SearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resourceModel = $resourceModel;
        $this->connectorFactory = $connectorFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function save($connector)
    {
        try {
            $this->resourceModel->save($connector);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__($exception->getMessage()));
        }

        return $connector;
    }

    public function getById($id)
    {
        if (isset($this->connectors[$id])) {
            return $this->connectors[$id];
        }

        $connector = $this->connectorFactory->create();
        $this->resourceModel->load($connector, $id);

        if (!$connector->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('The connector with the "%1" ID doesn\'t exist.', $id));
        }

        $this->connectors[$id] = $connector;

        return $this->connectors[$id];
    }

    public function getList($searchCriteria)
    {
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }

    public function getByProviderId($providerId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(\MageSuite\ErpConnector\Model\Data\Connector::PROVIDER_ID, $providerId)
            ->create();

        $list = $this->getList($searchCriteria);

        if (!$list->getTotalCount()) {
            return [];
        }

        return $list->getItems();
    }

    public function delete($connector)
    {
        try {
            $this->resourceModel->delete($connector);
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
