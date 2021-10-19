<?php
namespace Mageplaza\GiftCard\Plugin;

use Magento\Checkout\Controller\Cart\CouponPost;

class PostCouponCodeValidation {
    public function beforeExecute(CouponPost $subject) {
        $couponCode = $subject->getRequest()->getParam('coupon_code');
        $giftCard = $this->getGiftCard($couponCode);
        if($giftCard){
            //write here!!!
            
        }
        // means that this request should remove the current coupon code from the quote
        $remove = $subject->getRequest()->getParam('remove');
        die;

        // do your stuff
    }

    protected function getGiftCard($code)
    {
        $giftCard = $this->giftCardFactory->create();
        $this->giftCardResource->load($giftCard, $code, 'code');
        if ($giftCard->getId()) {
            return $giftCard;
        }
    }
}
