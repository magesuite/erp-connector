<?php
namespace MageSuite\ErpConnector\Api\Data;

/**
 * @api
 */
interface SchedulerSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \MageSuite\ErpConnector\Api\Data\SchedulerInterface[]
     */
    public function getItems();

    /**
     * @param \MageSuite\ErpConnector\Api\Data\SchedulerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
