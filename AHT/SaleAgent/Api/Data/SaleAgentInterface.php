<?php

namespace AHT\SaleAgent\Api\Data;

interface SaleAgentInterface
{
    const ID = 'entity_id';
    const ORDER_ID = 'order_id';
    const ORDER_ITEM_ID = 'order_item_id';
    const ORDER_ITEM_SKU = 'order_item_sku';
    const ORDER_ITEM_PRICE = 'order_item_price';
    const COMMISSION_TYPE = 'commission_type';
    const COMMISSION_VALUE = 'commission_value';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get ORDER ID
     *
     * @return int|null
     */
    public function getOrderId();

    /**
     * Get ORDER ITEM ID
     *
     * @return int|null
     */
    public function getOrderItemId();

    /**
     * Get OREDER ITEM SKU
     *
     * @return string|null
     */
    public function getOrderItemSku();

    /**
     * Get ORDER ITEM PRICE
     *
     * @return string|null
     */
    public function getOrderItemPrice();

    /**
     * Get COMMISSION TYPE
     *
     * @return string|null
     */
    public function getCommissionType();

    /**
     * Get COMMISSION VALUE
     *
     * @return string|null
     */
    public function getCommissionValue();

    /**
     * Gets the created-at timestamp for the order.
     *
     * @return string|null Created-at timestamp.
     */
    public function getCreatedAt();

    /**
     * Set ID
     *
     * @param int $id
     * @return SaleAgentInterface
     */
    public function setId($id);

    /**
     * Set ORDER ID
     *
     * @param int $orderId
     * @return SaleAgentInterface
     */
    public function setOrderId($orderId);

    /**
     * Set ORDER ITEM ID
     *
     * @param int $orderItemId
     * @return SaleAgentInterface
     */
    public function setOrderItemId($orderItemId);

    /**
     * Set ORDER ITEM SKU
     *
     * @param string $orderItemSku
     * @return SaleAgentInterface
     */
    public function setOrderItemSku($orderItemSku);

    /**
     * Set ORDER ITEM PRICE
     *
     * @param string $orderItemPrice
     * @return SaleAgentInterface
     */
    public function setOrderItemPrice($orderItemPrice);

    /**
     * Set COMMISSION TYPE
     *
     * @param string $commissionType
     * @return SaleAgentInterface
     */
    public function setCommissionType($commissionType);

    /**
     * Set COMMISSION VALUE
     *
     * @param string $commissionValue
     * @return SaleAgentInterface
     */
    public function setCommissionValue($commissionValue);

    /**
     * Sets the created-at timestamp for the order.
     *
     * @param string $createdAt timestamp
     * @return $this
     */
    public function setCreatedAt($createdAt);
}
