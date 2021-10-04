<?php

namespace AHT\UiComponent\Plugin\Block\Checkout;
 
class LayoutProcessor
{
    public function afterProcess(\Magento\Checkout\Block\Checkout\LayoutProcessor $subject, $jsLayout)
    {
        $children = $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'];
        //custom input radio
        $children['custom_text'] = array_merge($children, [
            'component' => 'Magento_Ui/js/form/element/checkbox-set',
            'config' => [
                'customScope' => 'shippingAddress.custom_attributes',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/checkbox-set',
                'options' => [
                    ['value' => '1', 'label' => __('Giao hàng nhanh')],
                    ['value' => '2', 'label' => __('Giao hàng tiêu chuẩn')]
                ],
                'value' => '1' // giá trị mặc định sẽ được chọn
            ],
            'dataScope' => 'shippingAddress.custom_attributes.custom_radio',
            'provider' => 'checkoutProvider'
        ]);
        // custom input text
        $children['custom_radio'] = array_merge($children, [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => 'shippingAddress.custom_attributes',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input',
            ],
            'validation' => [
                'required-entry' => true,
                'max_text_length' => 255
            ],
            'dataScope' => 'shippingAddress.custom_attributes.custom_text',
            'provider' => 'checkoutProvider',
            'label' => 'Custom Text field',
            'visible' => true,
            'sortOrder' => 250,
            'id' => 'custom-text'
        ]);
        // custom input select
        $children['custom_select'] = array_merge($children, [
            'component' => 'Magento_Ui/js/form/element/select',
            'config' => [
                'customScope' => 'shippingAddress.custom_attributes.custom_select',
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/select',
                'options' => [
                    ['value' => '1', 'label' => __('Yes')],
                    ['value' => '2', 'label' => __('No')]
                ],
            ],
            'dataScope' => 'shippingAddress.custom_attributes.custom_select',
            'provider' => 'checkoutProvider',
            'label' => 'Custom Select Field',
            'visible' => true,
            'validation' => [],
            'sortOrder' => 270,
            'id' => 'custom-select'
        ]);
 
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children'] = $children;
        return $jsLayout;
 
    }
}