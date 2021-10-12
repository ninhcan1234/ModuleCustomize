<?php

namespace Mageplaza\GiftCard\Model;

class GiftCard extends \Magento\Framework\Model\AbstractModel implements \Mageplaza\GiftCard\Api\Data\GiftCardInterface
{
    const CACHE_TAG = 'mageplaza_giftcard_gift_code';
    /**#@+
     * gift's statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;


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
    protected $_eventPrefix = 'giftcard_code';

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
        $this->_init('Mageplaza\GiftCard\Model\ResourceModel\GiftCard');
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
     * Set ID
     *
     * @param int $id
     * @return GiftCardInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get gift code
     *
     * @return string|null
     */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * Set gift code
     *
     * @param string $giftCode
     * @return GiftCardInterface
     */
    public function setCode($giftCode)
    {
        return $this->setData(self::CODE, $giftCode);
    }

    /**
     * Get gift balance
     *
     * @return int|null
     */
    public function getBalance()
    {
        return $this->getData(self::BALANCE);
    }

    /**
     * Set gift balance
     *
     * @param int $balance
     * @return GiftCardInterface
     */
    public function setBalance($balance)
    {
        return $this->setData(self::BALANCE, $balance);
    }

    /**
     * Get gift amount
     *
     * @return int|null
     */
    public function getAmountUsed()
    {
        return $this->getData(self::AMOUNT_USED);
    }

    /**
     * Set gift amount
     *
     * @param int $amountUsed
     * @return GiftCardInterface
     */
    public function setAmountUsed($amountUsed)
    {
        return $this->setData(self::AMOUNT_USED, $amountUsed);
    }

    /**
     * Get Create from
     *
     * @return int|null
     */
    public function getCreateFrom(){
        return $this->getData(self::CREATE_FROM);
    }

    /**
     * Set Create from
     *
     * @param int $from
     * @return GiftCardInterface
     */
    public function setCreateFrom($from){
        return $this->setData(self::AMOUNT_USED, $from);
    }

    /**
     * Get createTime 
     *
     * @return string|null
     */
    public function getCreateAt()
    {
        return $this->getData(self::CREATE_AT);
    }

    /**
     * Set createTime 
     *
     * @param string $createTime
     * @return GiftCardInterface
     */
    public function setCreateAt($createTime)
    {
        return $this->setData(self::CREATE_AT, $createTime);
    }
}
