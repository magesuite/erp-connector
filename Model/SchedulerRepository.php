<?php
namespace MageSuite\ErpConnector\Model;

class SchedulerRepository implements \MageSuite\ErpConnector\Api\SchedulerRepositoryInterface
{
    protected $schedulers = [];

    /**
     * @var \MageSuite\ErpConnector\Model\ResourceModel\Scheduler
     */
    protected $resourceModel;

    /**
     * @var \MageSuite\ErpConnector\Model\Data\SchedulerFactory
     */
    protected $schedulerFactory;

    /**
     * @var \MageSuite\ErpConnector\Model\ResourceModel\Scheduler\CollectionFactory
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
        \MageSuite\ErpConnector\Model\ResourceModel\Scheduler $resourceModel,
        \MageSuite\ErpConnector\Model\Data\SchedulerFactory $schedulerFactory,
        \MageSuite\ErpConnector\Model\ResourceModel\Scheduler\CollectionFactory $collectionFactory,
        \Magento\Framework\Api\SearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->resourceModel = $resourceModel;
        $this->schedulerFactory = $schedulerFactory;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function save($scheduler)
    {
        try {
            $this->resourceModel->save($scheduler);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__($exception->getMessage()));
        }

        return $scheduler;
    }

    public function getById($id)
    {
        if (isset($this->schedulers[$id])) {
            return $this->schedulers[$id];
        }

        $scheduler = $this->schedulerFactory->create();
        $this->resourceModel->load($scheduler, $id);

        if (!$scheduler->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('The scheduler with the "%1" ID doesn\'t exist.', $id));
        }

        $this->schedulers[$id] = $scheduler;

        return $this->schedulers[$id];
    }

    public function getByProviderId($id)
    {
        $scheduler = $this->schedulerFactory->create();
        $this->resourceModel->load($scheduler, $id, 'provider_id');

        if (!$scheduler->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('The scheduler with the "%1" ID doesn\'t exist.', $id));
        }

        return $scheduler;
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
