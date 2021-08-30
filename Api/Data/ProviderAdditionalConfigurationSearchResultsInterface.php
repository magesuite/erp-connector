<?php
namespace MageSuite\ErpConnector\Api\Data;

interface ProviderAdditionalConfigurationSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \MageSuite\ErpConnector\Api\Data\ProviderAdditionalConfigurationInterface[]
     */
    public function getItems();

    /**
     * @param \MageSuite\ErpConnector\Api\Data\ProviderAdditionalConfigurationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
