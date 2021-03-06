<?php
namespace AHT\SaleAgent\Model\ResourceModel;

class SaleAgent extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('customer_sale_agent_entity', 'entity_id');
    }
}
