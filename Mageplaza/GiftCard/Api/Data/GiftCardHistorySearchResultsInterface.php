<?php
namespace Mageplaza\GiftCard\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for gift card search results.
 * @api
 * @since 100.0.2
 */
interface GiftCardHistorySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get posts list.
     *
     * @return \Mageplaza\GiftCard\Api\Data\GiftCardHistoryInterface[]
     */
    public function getItems();

    /**
     * Set posts list.
     *
     * @param \Mageplaza\GiftCard\Api\Data\GiftCardHistoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
