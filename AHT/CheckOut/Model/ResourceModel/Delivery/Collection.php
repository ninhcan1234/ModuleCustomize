<?php
namespace AHT\CheckOut\Model\ResourceModel\Delivery;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'aht_checkout_delivery_collection';
    protected $_eventObject = 'delivery_collection';

    /**
     * Define the resource model & the model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('AHT\CheckOut\Model\Delivery', 'AHT\CheckOut\Model\ResourceModel\Delivery');
    }
}
