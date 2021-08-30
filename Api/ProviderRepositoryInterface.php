<?php
namespace MageSuite\ErpConnector\Api;

/**
 * @api
 */
interface ProviderRepositoryInterface
{
    /**
     * Save.
     *
     * @param \MageSuite\ErpConnector\Api\Data\ProviderInterface $provider
     * @return \MageSuite\ErpConnector\Api\Data\ProviderInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\MageSuite\ErpConnector\Api\Data\ProviderInterface $provider);

    /**
     * @param int $id
     * @return \MageSuite\ErpConnector\Api\Data\ProviderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id);

    /**
     * @param $name
     * @return \MageSuite\ErpConnector\Api\Data\ProviderInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @deprecated Use getList with search criteria instead
     */
    public function getByName($name);

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     * @return \MageSuite\ErpConnector\Api\Data\ProviderSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null);

    /**
     * @param \MageSuite\ErpConnector\Api\Data\ProviderInterface $model
     * @return bool true on success
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\MageSuite\ErpConnector\Api\Data\ProviderInterface $model);

    /**
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById(int $id);
}
