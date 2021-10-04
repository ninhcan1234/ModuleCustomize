<?php
namespace AHT\SaleAgent\Model\Attribute\Source;

class IsAgent extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{   
    public function getAllOptions()
    {
        if($this->_options === null){
            $this->_options = [
                ['label' => __('No'), 'value'=> 0],
                ['label' => __('Yes'), 'value'=> 1],
            ];
        }
        return $this->_options;
    }
}
