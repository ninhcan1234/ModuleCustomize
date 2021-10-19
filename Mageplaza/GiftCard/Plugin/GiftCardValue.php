<?php

namespace Mageplaza\GiftCard\Plugin;



class GiftCardValue
{
    protected $giftCardResource;
    protected $giftCardFactory;
    protected $checkoutSession;
    protected $coupon;
    protected $saleRule;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Mageplaza\GiftCard\Model\ResourceModel\GiftCard $giftCardResource,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Magento\SalesRule\Model\Coupon $coupon,
        \Magento\SalesRule\Model\Rule $saleRule

    ) {
        $this->coupon = $coupon;
        $this->saleRule = $saleRule;
        $this->giftCardFactory = $giftCardFactory;
        $this->giftCardResource = $giftCardResource;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * Applied code.
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function afterGetCouponCode(\Magento\Checkout\Block\Cart\Coupon $subject)
    {   
        $rulesCollection = $this->saleRule->getCollection();
        $giftCard = $this->giftCardFactory->create();
        // $this->giftCardResource->load($giftCard, $code, 'code');
        // return 'ABC';
    }
}
