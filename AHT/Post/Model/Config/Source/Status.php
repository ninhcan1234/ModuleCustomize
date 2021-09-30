<?php
namespace AHT\Post\Model\Config\Source;

class Status implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            0 => [
                'label' => 'Enable',
                'value' => 1
            ],
            1 => [
                'label' => 'Disable',
                'value' => 0
            ],
        ];

        return $options;
    }
}