<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AHT\CheckOut\Model;

use Magento\Framework\Api\SearchResults;
use AHT\CheckOut\Api\Data\DeliverySearchResultsInterface;

/**
 * Service Data Object with Block search results.
 */
class DeliverySearchResults extends SearchResults implements DeliverySearchResultsInterface
{
}
