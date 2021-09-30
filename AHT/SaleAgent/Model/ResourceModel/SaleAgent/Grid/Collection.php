<?php

namespace AHT\SaleAgent\Model\ResourceModel\SaleAgent\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Search\AggregationInterface;
use AHT\SaleAgent\Model\ResourceModel\SaleAgent\Collection as SaleAgentCollection;

/**
 * Class Collection
 * Collection for displaying grid
 */
class Collection extends SaleAgentCollection implements SearchResultInterface
{
    /**
     * Resource initialization
     * @return $this
     */
   public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document',
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $storeManager,
            $connection,
            $resource
        );
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
    }

    /**
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @param AggregationInterface $aggregations
     *
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }


    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null
    ) {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     *
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }
   

    // private function getIdProductEavEntity($code)
    // {
    //     return $this->eavAttribute->getIdByCode(Product::ENTITY, $code);
    // }

    //  /**
    //  * Override _initSelect to add custom columns
    //  *
    //  * @return void
    //  */
    // protected function _initSelect()
    // {
    //     $connection = $this->_resource->getConnection();
    //     $saleOrder = $connection->getTableName('sales_order');
    //     $saleOrderItem = $connection->getTableName('sales_order_item');
    //     $catalogDecimal = $connection->getTableName('catalog_product_entity_decimal');

    //     parent::_initSelect();
    //     $saleAgentCollection = $this->saleAgentCollectionFactory->create();
    //     $saleAgentCollection->getSelect()
    //         ->joinInner(
    //             ['so' => $this->getTable('sales_order')],
    //             "main_table.order_id = so.increment_id"
    //         )
    //         ->joinLeft(
    //             ['soi' => $this->getTable('sales_order_item')],
    //             "main_table.order_item_sku = soi.sku and main_table.order_item_id = soi.product_id"
    //         )
    //         ->joinLeft(
    //             ['cped' => 'catalog_product_entity_decimal'],
    //             "main_table.commission_value = cped.value 
    //             and soi.product_id = cped.entity_id 
    //             and cped.attribute_id = '{$this->getIdProductEavEntity('commission_value')}'"
    //         );

    //     return $saleAgentCollection;
    // }

    //SELECT * FROM `customer_sale_agent_entity` AS `sa` 
    //LEFT JOIN sales_order as `so` 
        //  on sa.order_id =   `so`.increment_id 
    //LEFT JOIN sales_order_item as `soi` 
        //  on sa.order_item_sku =  `soi`.sku and sa.order_item_id = soi.product_id 
    //LEFT JOIN catalog_product_entity_decimal as `cped` 
        //  on sa.commission_value      =         `cped`.value AND soi.product_id = cped.entity_id 
    //LEFT JOIN eav_entity_attribute as 'eea` 
        //  on  cped.attribute_id =     `eea`.attribute_id; 
}
