<?php

namespace MageSuite\ErpConnector\Api;

/**
 * @api
 */
interface ConnectorConfigurationRepositoryInterface
{
    /**
     * @param \MageSuite\ErpConnector\Api\Data\ConnectorConfigurationInterface $model
     * @return \MageSuite\ErpConnector\Api\Data\ConnectorConfigurationInterface
     */
    public function save($model);

    /**
     * @param $id
     * @return \MageSuite\ErpConnector\Api\Data\ConnectorConfigurationInterface
     */
    public function getById($id);

    /**
     * @param $id
     * @return \MageSuite\ErpConnector\Model\ResourceModel\ConnectorConfiguration\Collection \\TODO: change to searchResult
     */
    public function getListByProviderId($id);

    /**
     * @param $id
     * @return \MageSuite\ErpConnector\Model\ResourceModel\ConnectorConfiguration\Collection \\TODO: change to searchResult
     */
    public function getListByConnectorId($id);

    /**
     * @param $id
     * @param $name
     * @return \MageSuite\ErpConnector\Api\Data\ConnectorConfigurationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByConnectorIdAndName($id, $name);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \MageSuite\ErpConnector\Api\Data\ConnectorConfigurationSearchResultsInterface
     */
    public function getList($searchCriteria);

    /**
     * @param \MageSuite\ErpConnector\Api\Data\ConnectorConfigurationInterface $model
     * @return bool true on success
     */
    public function delete($model);

    /**
     * @param int $entityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($entityId);
}
