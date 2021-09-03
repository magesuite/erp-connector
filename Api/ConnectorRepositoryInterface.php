<?php
namespace MageSuite\ErpConnector\Api;

/**
 * @api
 */
interface ConnectorRepositoryInterface
{
    /**
     * @param \MageSuite\ErpConnector\Api\Data\ConnectorInterface $connector
     * @return \MageSuite\ErpConnector\Api\Data\ConnectorInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save($connector);

    /**
     * @param $id
     * @return \MageSuite\ErpConnector\Api\Data\ConnectorInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param $providerId
     * @return \MageSuite\ErpConnector\Model\ResourceModel\Connector\Collection
     */
    public function getByProviderId($providerId);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return Data\ConnectorSearchResultsInterface
     */
    public function getList($searchCriteria);

    /**
     * @param \MageSuite\ErpConnector\Api\Data\ConnectorInterface $connector
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete($connector);

    /**
     * @param int $entityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($entityId);
}
