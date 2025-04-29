<?php
namespace MageSuite\ErpConnector\Api;

/**
 * @api
 */
interface SchedulerConnectorConfigurationRepositoryInterface
{
    /**
     * @param \MageSuite\ErpConnector\Model\Data\SchedulerConnectorConfiguration $schedulerConnectorConfiguration
     * @return \MageSuite\ErpConnector\Model\Data\SchedulerConnectorConfiguration
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save($schedulerConnectorConfiguration);

    /**
     * @param $id
     * @return \MageSuite\ErpConnector\Model\Data\SchedulerConnectorConfiguration
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param $schedulerId
     * @return \MageSuite\ErpConnector\Model\Data\SchedulerConnectorConfiguration
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBySchedulerId($schedulerId);

    /**
     * @param $providerId
     * @return \MageSuite\ErpConnector\Model\Data\SchedulerConnectorConfiguration
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByProviderId($providerId);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $criteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(?\Magento\Framework\Api\SearchCriteriaInterface $criteria = null);

    /**
     * @param \MageSuite\ErpConnector\Model\Data\SchedulerConnectorConfiguration $schedulerConnectorConfiguration
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete($schedulerConnectorConfiguration);

    /**
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
