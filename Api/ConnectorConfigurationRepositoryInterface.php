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
     * @param $providerId
     * @return \MageSuite\ErpConnector\Api\Data\ConnectorConfigurationSearchResultsInterface
     */
    public function getByProviderId($providerId);

    /**
     * @param $connectorId
     * @return \MageSuite\ErpConnector\Api\Data\ConnectorConfigurationSearchResultsInterface
     */
    public function getByConnectorId($connectorId);

    /**
     * @param $connectorId
     * @param $name
     * @return \MageSuite\ErpConnector\Api\Data\ConnectorConfigurationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getItemByConnectorIdAndName($connectorId, $name);

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
