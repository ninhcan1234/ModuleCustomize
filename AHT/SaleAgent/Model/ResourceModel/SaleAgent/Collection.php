<?php
namespace AHT\SaleAgent\Model\ResourceModel\SaleAgent;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Catalog\Model\Product;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    const YOUR_TABLE = 'customer_sale_agent_entity';

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_init(
            'AHT\SaleAgent\Model\SaleAgent',
            'AHT\SaleAgent\Model\ResourceModel\SaleAgent'
        );
        parent::__construct(
            $entityFactory, $logger, $fetchStrategy, $eventManager, $connection,
            $resource
        );
        $this->storeManager = $storeManager;
    }
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()
        ->joinLeft(
            ['soi' => $this->getTable('sales_order_item')],
            'main_table.order_item_id = soi.product_id',
            ['name']
        );
    }
    
    private function getIdProductEavEntity($attribute_code)
    {
        return $this->eavAttribute->getIdByCode(Product::ENTITY, $attribute_code);
    }

    //catalog_product_entity_int.value = customer_entity.entity_id;
    //catalog_product_entity_int.attribute_id = eav_attribute.attribute_id = 

}
