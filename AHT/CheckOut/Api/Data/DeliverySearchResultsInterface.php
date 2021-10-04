<?php
namespace AHT\CheckOut\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for delivery search results.
 * @api
 * @since 100.0.2
 */
interface DeliverySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get deliverys list.
     *
     * @return \AHT\CheckOut\Api\Data\DeliveryInterface[]
     */
    public function getItems();

    /**
     * Set deliverys list.
     *
     * @param \AHT\CheckOut\Api\Data\DeliveryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
