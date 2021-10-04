<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 */
namespace AHT\SaleAgent\Plugin;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Option\ArrayInterface;
 
class FindDuplicateEntry
{
    /**
     * @param \Magento\Eav\Model\Entity\Collection\AbstractCollection $subject
     * @param \Closure $process
     * @param \Magento\Framework\DataObject $dataObject
     * @return $this
     */
    public function aroundAddItem(\Magento\Eav\Model\Entity\Collection\AbstractCollection $subject, \Closure $process, \Magento\Framework\DataObject $dataObject)
    {
        try{
            return $process($dataObject);
        }catch ( \Exception $e){
            return $this;
        }
    }
}