<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mageplaza\GiftCard\Model;

use Magento\Framework\Api\SearchResults;
use Mageplaza\GiftCard\Api\Data\GiftCardSearchResultsInterface;

/**
 * Service Data Object with Gift search results.
 */
class GiftCardSearchResults extends SearchResults implements GiftCardSearchResultsInterface
{
}
