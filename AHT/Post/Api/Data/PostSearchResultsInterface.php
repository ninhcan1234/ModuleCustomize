<?php
namespace AHT\Post\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for cms block search results.
 * @api
 * @since 100.0.2
 */
interface PostSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get posts list.
     *
     * @return \AHT\Post\Api\Data\PostInterface[]
     */
    public function getItems();

    /**
     * Set posts list.
     *
     * @param \AHT\Post\Api\Data\PostInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
