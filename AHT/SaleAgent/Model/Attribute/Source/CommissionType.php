<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace AHT\SaleAgent\Model\Attribute\Source;

class CommissionType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Get all options
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => __('Choose Type'), 'value' => ''],
                ['label' => __('Percent'), 'value' => 'percent'],
                ['label' => __('Fixed'), 'value' => 'fixed'],
            ];
        }
        return $this->_options;
    }
}