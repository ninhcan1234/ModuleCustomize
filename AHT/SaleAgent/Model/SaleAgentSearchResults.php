<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AHT\SaleAgent\Model;

use Magento\Framework\Api\SearchResults;
use AHT\SaleAgent\Api\Data\SaleAgentSearchResultsInterface;

/**
 * Service Data Object with Sale Agent search results.
 */
class SaleAgentSearchResults extends SearchResults implements SaleAgentSearchResultsInterface
{
}
