<?php
namespace MageSuite\ErpConnector\Api\Data;

interface ConnectorConfigurationSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \MageSuite\ErpConnector\Api\Data\ConnectorInterface[]
     */
    public function getItems();

    /**
     * @param \MageSuite\ErpConnector\Api\Data\ConnectorInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
