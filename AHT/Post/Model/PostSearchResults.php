<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AHT\Post\Model;

use Magento\Framework\Api\SearchResults;
use AHT\Post\Api\Data\PostSearchResultsInterface;

/**
 * Service Data Object with Block search results.
 */
class PostSearchResults extends SearchResults implements PostSearchResultsInterface
{
}
