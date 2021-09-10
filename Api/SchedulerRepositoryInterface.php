<?php
namespace MageSuite\ErpConnector\Api;

/**
 * @api
 */
interface SchedulerRepositoryInterface
{
    /**
     * @param \MageSuite\ErpConnector\Model\Data\Scheduler $scheduler
     * @return \MageSuite\ErpConnector\Model\Data\Scheduler
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save($scheduler);

    /**
     * @param $id
     * @return \MageSuite\ErpConnector\Model\Data\Scheduler
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param $providerId
     * @return \MageSuite\ErpConnector\Model\Data\Scheduler
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @deprecated Use getList with search criteria instead
     */
    public function getByProviderId($providerId);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList($searchCriteria = null);

    /**
     * @param \MageSuite\ErpConnector\Model\Data\Scheduler $scheduler
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete($scheduler);

    /**
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
