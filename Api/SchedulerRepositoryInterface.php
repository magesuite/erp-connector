<?php
namespace MageSuite\ErpConnector\Api;

/**
 * @api
 */
interface SchedulerRepositoryInterface
{
    /**
     * @param \MageSuite\ErpConnector\Api\Data\SchedulerInterface $model
     * @return \MageSuite\ErpConnector\Api\Data\SchedulerInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save($model);

    /**
     * @param $id
     * @return \MageSuite\ErpConnector\Api\Data\SchedulerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param $providerId
     * @return \MageSuite\ErpConnector\Api\Data\SchedulerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @deprecated Use getList with search criteria instead
     */
    public function getByProviderId($providerId);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     * @return \MageSuite\ErpConnector\Api\Data\SchedulerSearchResultsInterface
     */
    public function getList($searchCriteria = null);

    /**
     * @param \MageSuite\ErpConnector\Api\Data\SchedulerInterface $model
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete($model);

    /**
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
