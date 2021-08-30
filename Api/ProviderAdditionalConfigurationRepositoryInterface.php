<?php
namespace MageSuite\ErpConnector\Api;

interface ProviderAdditionalConfigurationRepositoryInterface
{
    /**
     * @param \MageSuite\ErpConnector\Api\Data\ProviderAdditionalConfigurationInterface $model
     * @return \MageSuite\ErpConnector\Api\Data\ProviderAdditionalConfigurationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save($model);

    /**
     * @param $id
     * @return \MageSuite\ErpConnector\Api\Data\ProviderAdditionalConfigurationInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * @param $id
     * @return \MageSuite\ErpConnector\Model\ResourceModel\ProviderAdditionalConfiguration\Collection \\TODO: change to searchResult
     */
    public function getCollectionByProviderId($id);

    /**
     * @param $id
     * @return \MageSuite\ErpConnector\Api\Data\ProviderAdditionalConfigurationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByProviderId($id);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \MageSuite\ErpConnector\Api\Data\ProviderAdditionalConfigurationSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList($searchCriteria);

    /**
     * @param \MageSuite\ErpConnector\Api\Data\ProviderAdditionalConfigurationInterface $model
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete($model);

    /**
     * @param string $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
