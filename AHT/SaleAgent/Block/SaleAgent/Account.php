<?php

namespace AHT\SaleAgent\Block\SaleAgent;

class Account extends \Magento\Framework\View\Element\Template
{
    //Type of commission
    CONST FIXED = 'fixed';
    CONST PERCENT = 'percent';

    protected $customerCollectionFactory;
    protected $productCollectionFactory;
    protected $customerSession;
    protected $resource;
    public $priceHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        array $data = []
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->customerSession = $customerSession;
        $this->priceHelper  = $priceHelper;
        $this->resource = $resource;
        parent::__construct($context, $data);
    }

    public function filterProductHasSaleAgent()
    {
        $connection = $this->resource->getConnection();
        $saleAgentTable = $connection->getTableName('customer_sale_agent_entity');
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*')->addAttributeToFilter('sale_agent_id', $this->getIdCustomerBySession());
        $collection->getSelect()
        ->joinInner(
            ['sa'=> $saleAgentTable],
            'e.entity_id = sa.order_item_id',
            [
                'sa.entity_id as id',
                'sa.order_id',
                'sa.total_order_item',
                'sa.order_item_price',
                'sa.commission_type',
                'sa.commission_value',
                'sa.current_commission_value',
            ]
        )->group('order_item_id')
        ->columns(['total' => new \Zend_Db_Expr('SUM(sa.total_order_item)')])
        ->columns(['total_price' => new \Zend_Db_Expr('SUM(order_item_price)')]);

        return $collection->setPageSize(5);
    }

    public function getIdCustomerBySession()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->customerSession->getCustomerId();
        }
    }

    public function formatPrice($price) {
        return $this->priceHelper->currency($price,true);
    }

    public function checkIsSaleAgent(){
        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('*')->addAttributeToFilter('sale_agent_id', $this->getIdCustomerBySession());
        $collection === null ? $check = false : $check = true;
        return $check;
    }
}
