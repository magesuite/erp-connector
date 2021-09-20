<?php
namespace MageSuite\ErpConnector\Api;

/**
 * @api
 */
interface ProviderRepositoryInterface
{
    /**
     * @param \MageSuite\ErpConnector\Model\Data\Provider $provider
     * @return \MageSuite\ErpConnector\Model\Data\Provider
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save($provider);

    /**
     * @param int $id
     * @return \MageSuite\ErpConnector\Model\Data\Provider
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param $name
     * @return \MageSuite\ErpConnector\Model\Data\Provider
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByName($name);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null);

    /**
     * @param \MageSuite\ErpConnector\Model\Data\Provider $provider
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete($provider);

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById(int $id);
}
