<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace AHT\SaleAgent\Model\Attribute\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;

class SaleAgent extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource implements OptionSourceInterface
{
    protected $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Validate
     * @param \Magento\Catalog\Model\Product $object
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool
     */
    public function validate($object)
    {
        $value = $object->getData($this->getAttribute()->getAttributeCode());
        if (($object->getAttributeSetId() == 10) && ($value == 'wool')) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Bottom can not be wool.')
            );
        }
        return true;
    }

    /**
     * Get all options
     * @return array
     */
    public function getAllOptions()
    {
        $customers = $this->getCustomerCollection();
        $this->_options= [['label' => __('Choose Sale Agent'), 'value' => '']];
        foreach($customers as $customer){
            $data = ['label' => $customer->getName(), 'value' => $customer->getEntityId()];
            array_push($this->_options, $data);
        }
        return $this->_options;
    }

    protected function getCustomerCollection(){
        $collection = $this->collectionFactory->create()->addAttributeToFilter('is_sale_agent', 1);
        return $collection;
    }
}
