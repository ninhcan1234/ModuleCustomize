<?php

namespace Mageplaza\GiftCard\Model;

use Mageplaza\GiftCard\Api\Data\GiftCardHistoryInterface;

class GiftCardHistory extends \Magento\Framework\Model\AbstractModel implements GiftCardHistoryInterface
{
    const CACHE_TAG = 'mageplaza_giftcard_gift_card_history';

    /**
     * Model cache tag for clear cache in after save and after delete
     *
     * @var string
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'giftcard_history';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mageplaza\GiftCard\Model\ResourceModel\GiftCardHistory');
    }

    /**
     * Return a unique id for the model.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set HISTORY ID
     *
     * @param int $id
     * @return GiftCardHistoryInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

     /**
     * Get HISTORY ID
     *
     * @return int|null
     */
    public function getGiftCardId()
    {
        return $this->getData(self::GIFTCARD_ID);
    }

    /**
     * Set GIFTCARD ID
     *
     * @param int $giftCardId
     * @return GiftCardHistoryInterface
     */
    public function setGiftCardId($giftCardId)
    {
        return $this->setData(self::GIFTCARD_ID, $giftCardId);
    }

       /**
     * Get CUSTOMER ID
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Set CUSTOMER ID
     *
     * @param int $customerId
     * @return GiftCardHistoryInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get history amount
     *
     * @return int|null
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * Set history amount
     *
     * @param int $amount
     * @return GiftCardHistoryInterface
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * Get Create from
     *
     * @return int|null
     */
    public function getAction(){
        return $this->getData(self::ACTION);
    }

    /**
     * Set ACTION
     *
     * @param int $action
     * @return GiftCardHistoryInterface
     */
    public function setAction($action){
        return $this->setData(self::ACTION, $action);
    }

    /**
     * Get actionTime 
     *
     * @return string|null
     */
    public function getActionTime()
    {
        return $this->getData(self::ACTION_TIME);
    }

    /**
     * Set ACTION TIME 
     *
     * @param string $actionTime
     * @return GiftCardHistoryInterface
     */
    public function setActionTime($actionTime)
    {
        return $this->setData(self::ACTION_TIME, $actionTime);
    }
}
