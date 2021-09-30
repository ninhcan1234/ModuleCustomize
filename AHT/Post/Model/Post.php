<?php

namespace AHT\Post\Model;

class Post extends \Magento\Framework\Model\AbstractModel implements \AHT\Post\Api\Data\PostInterface
{
    const CACHE_TAG = 'aht_post_post';

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
    protected $_eventPrefix = 'post';

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
        $this->_init(ResourceModel\Post::class);
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

    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    public function getId(){
        return $this->getData(self::ID);
    }

    
    public function setId($id){
        $this->setData(self::ID, $id);
        return $this;
    }

    public function getName()
    {
        return $this->getData(self::NAME);
    }

    public function setName($name)
    {
        $this->setData(self::NAME, $name);
        return $this;
    }

    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function setDescription($description)
    {
        $this->setData(self::DESCRIPTION, $description);
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt($created_at)
    {
        $this->setData(self::CREATED_AT, $created_at);
        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function setUpdatedAt($updated_at)
    {
        $this->setData(self::UPDATED_AT, $updated_at);
        return $this;
    }

    public function getImage(){
        return $this->getData(self::IMAGE);
    }

    public function setImage($image)
    {
        $this->setData(self::IMAGE, $image);
        return $this;
    }

    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);
        return $this;
    }
}
