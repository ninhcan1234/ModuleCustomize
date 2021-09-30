<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace AHT\UiComponent\Block\Adminhtml\Customer\Attribute\Button;

/**
 * Class Cancel
 */
class Cancel extends Generic
{
   /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Cancel'),
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }
}
