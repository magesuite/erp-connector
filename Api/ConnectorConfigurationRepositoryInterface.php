<?php

namespace MageSuite\ErpConnector\Api;

/**
 * @api
 */
interface ConnectorConfigurationRepositoryInterface
{
    /**
     * @param \MageSuite\ErpConnector\Model\Data\ConnectorConfiguration $connectorConfiguration
     * @return \MageSuite\ErpConnector\Model\Data\ConnectorConfiguration
     */
    public function save($connectorConfiguration);

    /**
     * @param $id
     * @return \MageSuite\ErpConnector\Model\Data\ConnectorConfiguration
     */
    public function getById($id);

    /**
     * @param $providerId
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getByProviderId($providerId);

    /**
     * @param $connectorId
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getByConnectorId($connectorId);

    /**
     * @param $connectorId
     * @param $name
     * @return \MageSuite\ErpConnector\Model\Data\ConnectorConfiguration
     */
    public function getItemByConnectorIdAndName($connectorId, $name);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList($searchCriteria);

    /**
     * @param \MageSuite\ErpConnector\Model\Data\ConnectorConfiguration $connectorConfiguration
     * @return bool true on success
     */
    public function delete($connectorConfiguration);

    /**
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
