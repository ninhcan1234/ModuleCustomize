<?php
namespace AHT\SaleAgent\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for sale agent search results.
 * @api
 * @since 100.0.2
 */
interface SaleAgentSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get saleagent list.
     *
     * @return \AHT\SaleAgent\Api\Data\SaleAgentInterface[]
     */
    public function getItems();

    /**
     * Set saleagent list.
     *
     * @param \AHT\SaleAgent\Api\Data\SaleAgentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
