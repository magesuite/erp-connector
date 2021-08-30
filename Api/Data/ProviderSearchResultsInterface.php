<?php
namespace MageSuite\ErpConnector\Api\Data;

interface ProviderSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \MageSuite\ErpConnector\Api\Data\ProviderInterface[]
     */
    public function getItems();

    /**
     * @param \MageSuite\ErpConnector\Api\Data\ProviderInterface[]|\Magento\Framework\DataObject[] $items
     * @return $this
     */
    public function setItems(array $items);
}
