<?php

namespace AHT\SaleAgent\Block\SaleAgent;

class Account extends \Magento\Framework\View\Element\Template
{

    protected $customerCollectionFactory;

    protected $productCollectionFactory;

    protected $customerSession;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $data);
    }


    public function getIdCustomerBySession()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->customerSession->getCustomerId();
        }
    }

    public function filterProduct()
    {
        $collection = $this->productCollectionFactory->create();
        $data = $collection->getSelect()->joinInner(
                ['sa' => 'customer_sale_agent_entity'],
                'sku = sa.sku'
            );
        return $data;
    }

    public function filterSaleAgent(){
        $collection = $this->customerCollectionFactory->create();
        $isSaleAgent = $collection->addAttributeToFilter('is_sale_agent', 1);
        return $isSaleAgent;
    }
}
