<?php
namespace AHT\CheckOut\Api\Data;

interface DeliveryInterface
{
   /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = 'entity_id';
    const QUOTE_ID = 'quote_id';
    const ORDER_ID = 'order_id';
    const COMMENT = 'delivery_comment';
    const DATE = 'delivery_date';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return DeliveryInterface
     */
    public function setId($id);

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getQuoteId();

    /**
     * Set ID
     *
     * @param int $quoteId
     * @return DeliveryInterface
     */
    public function setQuoteId($quoteId);

     /**
     * Get ID
     *
     * @return int|null
     */
    public function getOrderId();

        /**
     * Set ID
     *
     * @param int $orderId
     * @return DeliveryInterface
     */
    public function setOrderId($orderId);

     /**
     * Get delivery date time
     *
     * @return string|null
     */
    public function getDeliveryDate();

    /**
     * Set delivery date time
     *
     * @param string $date
     * @return PostInterface
     */
    public function setDeliveryDate($date);

    /**
     * Get description
     *
     * @return string|null
     */
    public function getDeliveryComment();


    /**
     * Set comment
     *
     * @param string $comment
     * @return DeliveryInterface
     */
    public function setDeliveryComment($comment);

}
