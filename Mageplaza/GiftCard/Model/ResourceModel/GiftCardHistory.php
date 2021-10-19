<?php
namespace Mageplaza\GiftCard\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;

class GiftCardHistory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('giftcard_history', 'history_id');
    }

    // protected function _beforeSave(AbstractModel $object)
    // {
    //     if ($object->getAmount() == 0) {
    //         throw new \Magento\Framework\Exception\LocalizedException(
    //             __('This gift card code has expired, please use another code!')
    //         );
    //     } 
    //     return parent::_beforeSave($object);
    // }
}
