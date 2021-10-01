<?php
namespace AHT\SaleAgent\Model\ResourceModel\SaleAgent;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     * 
     */
    const YOUR_TABLE = 'customer_sale_agent_entity';

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
            'AHT\SaleAgent\Model\SaleAgent',
            'AHT\SaleAgent\Model\ResourceModel\SaleAgent'
        );
        parent::__construct(
            $entityFactory, $logger, $fetchStrategy, $eventManager, $connection,
            $resource
        );
    }

    protected function getIdProductEavEntity($attribute_code)
    {
        return $this->eavAttribute->getIdByCode(\Magento\Catalog\Model\Product::ENTITY, $attribute_code);
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
        ->joinLeft(
            ['soi' => $this->getTable('sales_order_item')],  
            'main_table.order_item_id = soi.product_id',   
            ['name','sku','main_table.total_order_item']
        )
        ->joinLeft(
            ['cpei'=>$this->getTable('catalog_product_entity_int')],
            'soi.product_id = cpei.entity_id',
            ['value']
        )
        ->joinLeft(
            ['ce' => $this->getTable('customer_entity')],
            'cpei.value = ce.entity_id',
            ['fullname' => "CONCAT(ce.firstname, ' ', ce.lastname)"]
        )
        ->where(
            "cpei.attribute_id = {$this->getIdProductEavEntity('sale_agent_id')}"
        );
         
    }
}
