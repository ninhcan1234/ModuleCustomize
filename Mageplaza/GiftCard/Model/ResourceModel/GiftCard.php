<?php

namespace Mageplaza\GiftCard\Model\ResourceModel;

class GiftCard extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('giftcard_code', 'giftcard_id');
    }

    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getAmountUsed() > $object->getBalance()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Amount used value can\'t be greater than balance value!')
            );
        } 
        return parent::_beforeSave($object);
    }

    
}
