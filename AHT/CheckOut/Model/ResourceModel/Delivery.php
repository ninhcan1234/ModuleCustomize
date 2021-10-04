<?php
namespace AHT\CheckOut\Model\ResourceModel;

class Delivery extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('aht_delivery_step', 'entity_id');
    }
}
