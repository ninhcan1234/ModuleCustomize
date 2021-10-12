<?php

namespace Mageplaza\GiftCard\Api\Data;

interface GiftCardInterface
{
  /**#@+
   * Constants for keys of data array. Identical to the name of the getter in snake case
   */
  const ID = 'giftcard_id';
  const CODE = 'code';
  const BALANCE = 'balance';
  const AMOUNT_USED = 'amount_used';
  const CREATE_FROM = 'create_from';
  const CREATE_AT = 'create_at';

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
   * @return GiftCardInterface
   */
  public function setId($id);

  /**
   * Get gift code
   *
   * @return string|null
   */
  public function getCode();

  /**
   * Set gift code
   *
   * @param string $giftCode
   * @return GiftCardInterface
   */
  public function setCode($giftCode);

  /**
   * Get gift balance
   *
   * @return int|null
   */
  public function getBalance();

  /**
   * Set gift balance
   *
   * @param int $balance
   * @return GiftCardInterface
   */
  public function setBalance($balance);

    /**
   * Get gift amount
   *
   * @return int|null
   */
  public function getAmountUsed();

  /**
   * Set gift amount
   *
   * @param int $amountUsed
   * @return GiftCardInterface
   */
  public function setAmountUsed($amountUsed);

  /**
   * Get Create from
   *
   * @return int|null
   */
  public function getCreateFrom();

  /**
   * Set Create from
   *
   * @param int $from
   * @return GiftCardInterface
   */
  public function setCreateFrom($from);


  /**
   * Get createTime 
   *
   * @return string|null
   */
  public function getCreateAt();

  /**
   * Set createTime 
   *
   * @param string $createTime
   * @return GiftCardInterface
   */
  public function setCreateAt($createTime);
}
