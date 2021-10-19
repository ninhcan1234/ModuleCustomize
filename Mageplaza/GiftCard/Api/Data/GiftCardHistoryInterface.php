<?php

namespace Mageplaza\GiftCard\Api\Data;

interface GiftCardHistoryInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = 'history_id';
    const GIFTCARD_ID = 'giftcard_id';
    const CUSTOMER_ID = 'customer_id';
    const AMOUNT = 'amount';
    const ACTION = 'action';
    const ACTION_TIME = 'action_time';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set HISTORY ID
     *
     * @param int $id
     * @return GiftCardHistoryInterface
     */
    public function setId($id);

    /**
     * Get HISTORY ID
     *
     * @return int|null
     */
    public function getGiftCardId();

    /**
     * Set GIFTCARD ID
     *
     * @param int $giftCardId
     * @return GiftCardHistoryInterface
     */
    public function setGiftCardId($giftCardId);

    /**
     * Get CUSTOMER ID
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Set CUSTOMER ID
     *
     * @param int $customerId
     * @return GiftCardHistoryInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get history amount
     *
     * @return int|null
     */
    public function getAmount();

    /**
     * Set history amount
     *
     * @param int $amount
     * @return GiftCardHistoryInterface
     */
    public function setAmount($amount);

    /**
     * Get Create from
     *
     * @return int|null
     */
    public function getAction();

    /**
     * Set ACTION
     *
     * @param int $action
     * @return GiftCardHistoryInterface
     */
    public function setAction($action);

    /**
     * Get actionTime 
     *
     * @return string|null
     */
    public function getActionTime();


    /**
     * Set ACTION TIME 
     *
     * @param string $actionTime
     * @return GiftCardHistoryInterface
     */
    public function setActionTime($actionTime);
}
