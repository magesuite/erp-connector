<?php
namespace MageSuite\ErpConnector\Api;

interface ProviderAdditionalConfigurationRepositoryInterface
{
    /**
     * @param \MageSuite\ErpConnector\Model\Data\ProviderAdditionalConfiguration $providerAdditionalConfiguration
     * @return \MageSuite\ErpConnector\Model\Data\ProviderAdditionalConfiguration
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save($providerAdditionalConfiguration);

    /**
     * @param $id
     * @return \MageSuite\ErpConnector\Model\Data\ProviderAdditionalConfiguration
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);

    /**
     * @param $providerId
     * @return \MageSuite\ErpConnector\Model\Data\ProviderAdditionalConfiguration
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByProviderId($providerId);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList($searchCriteria);

    /**
     * @param \MageSuite\ErpConnector\Model\Data\ProviderAdditionalConfiguration $providerAdditionalConfiguration
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete($providerAdditionalConfiguration);

    /**
     * @param string $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
