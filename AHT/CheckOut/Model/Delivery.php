<?php
namespace AHT\CheckOut\Model;

class Delivery extends \Magento\Framework\Model\AbstractModel implements \AHT\CheckOut\Api\Data\DeliveryInterface
{
    const CACHE_TAG = 'aht_checkout_delivery';

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
    protected $_eventPrefix = 'delivery';

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
        $this->_init('AHT\CheckOut\Model\ResourceModel\Delivery');
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

    public function getId(){
        return $this->getData(self::ID);
    }

    
    public function setId($id){
        $this->setData(self::ID, $id);
        return $this;
    }

    public function getQuoteId(){
        return $this->getData(self::QUOTE_ID);
    }

    
    public function setQuoteId($quoteId){
        $this->setData(self::QUOTE_ID, $quoteId);
        return $this;
    }

    public function getOrderId(){
        return $this->getData(self::ORDER_ID);
    }

    public function setOrderId($orderId){
        $this->setData(self::ORDER_ID, $orderId);
        return $this;
    }

    public function getDeliveryComment()
    {
        return $this->getData(self::COMMENT);
    }

    public function setDeliveryComment($comment)
    {
        $this->setData(self::COMMENT, $comment);
        return $this;
    }

    public function getDeliveryDate()
    {
        return $this->getData(self::DATE);
    }

    public function setDeliveryDate($date)
    {
        $this->setData(self::DATE, $date);
        return $this;
    }
}
