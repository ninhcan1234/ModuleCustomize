<?php

namespace AHT\SaleAgent\Block;

use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use AHT\SaleAgent\Model\ResourceModel\SaleAgent\CollectionFactory as SaleAgentCollectionFactory;

class Load extends \Magento\Framework\View\Element\Template
{
    protected $mainTable = 'customer_sale_agent_entity';
    protected $saleAgentCollectionFactory;
    protected $attribute;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = [],
        SaleAgentCollectionFactory $saleAgentCollectionFactory,
        Attribute $attribute

    ) {
        parent::__construct($context, $data);
        $this->attribute = $attribute;
        $this->saleAgentCollectionFactory = $saleAgentCollectionFactory;
    }

    public function join()
    {
        $saleAgentCollection = $this->saleAgentCollectionFactory->create();
        $saleAgentCollection->getSelect()
        ->joinLeft(
            ['so' => 'sales_order'],
            "main_table.order_id = so.increment_id"
        );

        return $saleAgentCollection;
    }
    public function test(){
        return 'abc';
    }

    public function getIdProductEavEntity($attribute_code)
    {
        return $this->attribute->getIdByCode(\Magento\Catalog\Model\Product::ENTITY, $attribute_code);
    }


    //main.order_item_id == sales_order_item.product_id
    //main.order_item_sku == sales_order_item.sku
    //main.order_item_price == sales_order_item.price(Caculated Fee Ship)
    //sales_order.customer_id == customer_entity.entity_id
}
