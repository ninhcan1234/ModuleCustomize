<?php

/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_ProductFeed
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductFeed\Plugin\Model\Condition\Product;

use Magento\CatalogInventory\Model\Source\Stock;
use Magento\CatalogRule\Model\Rule\Condition\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\Rule\Model\Condition\Product\AbstractProduct as AbstractProductRule;
use Mageplaza\ProductFeed\Helper\Data;

/**
 * Class AbstractProduct
 * @package Mageplaza\ProductFeed\Plugin\Model\Condition\Product
 */
class AbstractProduct
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var Stock
     */
    protected $stockStatus;

    /**
     * @var Type
     */
    protected $typeStatus;

    /**
     * AbstractProduct constructor.
     *
     * @param Data $helperData
     * @param Stock $stockStatus
     */
    public function __construct(
        Data $helperData,
        Stock $stockStatus,
        Type $typeStatus
    ) {
        $this->helperData  = $helperData;
        $this->stockStatus = $stockStatus;
        $this->typeStatus  = $typeStatus;
    }

    /**
     * @param AbstractProductRule $subject
     * @param Product $result
     *
     * @return mixed
     */
    public function afterLoadAttributeOptions(AbstractProductRule $subject, $result)
    {
        if ($this->helperData->isEnabled()) {
            $attributes = $result->getAttributeOption();
            if (!array_key_exists('is_in_stock', $attributes)) {
                $attributes['is_in_stock'] = __('Stock Status');
                $result->setAttributeOption($attributes);
            }
            if (!array_key_exists('product_types', $attributes)) {
                $attributes['product_types'] = __('Product Types');
                $result->setAttributeOption($attributes);
            }
        }

        return $result;
    }

    /**
     * @param AbstractProductRule $subject
     * @param callable $proceed
     *
     * @return string
     */
    public function aroundGetInputType(AbstractProductRule $subject, callable $proceed)
    {
        switch ($subject->getAttribute()) {
            case 'is_in_stock':
                return 'select';
            case 'quantity_and_stock_status':
                return 'string';
            case 'product_types':
                return 'select';
            default:
                return $proceed();
        }
    }

    /**
     * @param AbstractProductRule $subject
     * @param callable $proceed
     *
     * @return string
     */
    public function aroundGetValueElementType(AbstractProductRule $subject, callable $proceed)
    {
        switch ($subject->getAttribute()) {
            case 'is_in_stock':
                return 'select';
            case 'product_types':
                return 'select';
            case 'quantity_and_stock_status':
                return 'text';
            default:
                return $proceed();
        }
    }

    /**
     * @param AbstractProductRule $subject
     * @param null|array $result
     *
     * @return null|array
     */
    public function afterGetValueSelectOptions(AbstractProductRule $subject, $result)
    {
        if ($subject->getAttribute() === 'is_in_stock') {
            $result = $this->stockStatus->getAllOptions();
        }
        if ($subject->getAttribute() === 'product_types') {
            $result = $this->typeStatus->getAllOptions();
        }

        return $result;
    }

    /**
     * @param AbstractProductRule $subject
     * @param callable $proceed
     * @param null $productCollection
     *
     * @return AbstractProductRule
     */
    public function aroundCollectValidatedAttributes(
        AbstractProductRule $subject,
        callable $proceed,
        $productCollection
    ) {
        if ($subject->getAttribute() === 'is_in_stock') {
            return $subject;
        }
        if ($subject->getAttribute() === 'product_types') {
            return $subject;
        }

        return $proceed($productCollection);
    }
}
