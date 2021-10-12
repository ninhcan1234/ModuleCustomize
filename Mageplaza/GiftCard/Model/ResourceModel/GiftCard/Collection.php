<?php
namespace Mageplaza\GiftCard\Model\ResourceModel\GiftCard;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     * 
     */
    const YOUR_TABLE = 'giftcard_code';

    protected $eavAttribute;

    public function __construct(
        Attribute $eavAttribute,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->storeManager = $storeManager;
        $this->eavAttribute = $eavAttribute;
        $this->_init(
            'Mageplaza\GiftCard\Model\GiftCard',
            'Mageplaza\GiftCard\Model\ResourceModel\GiftCard'
        );
        parent::__construct(
            $entityFactory, $logger, $fetchStrategy,
            $eventManager, $connection, $resource
        );
    }

    protected function getIdProductEavEntity($attribute_code)
    {
        return $this->eavAttribute->getIdByCode(\Magento\Catalog\Model\Product::ENTITY, $attribute_code);
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        return $this;
    }
}
