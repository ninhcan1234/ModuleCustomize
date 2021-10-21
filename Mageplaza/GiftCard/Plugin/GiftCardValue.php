<?php

namespace Mageplaza\GiftCard\Plugin;

class GiftCardValue
{
    protected $giftCardResource;
    protected $giftCardFactory;
    protected $sessionFactory;
    protected $quoteFactory;

    public function __construct(
        \Mageplaza\GiftCard\Model\ResourceModel\GiftCard $giftCardResource,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Magento\Checkout\Model\SessionFactory $sessionFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory

    ) {
        $this->giftCardFactory = $giftCardFactory;
        $this->giftCardResource = $giftCardResource;
        $this->sessionFactory = $sessionFactory;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * Applied code.
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function afterGetCouponCode(\Magento\Checkout\Block\Cart\Coupon $subject)
    {
        if ($this->getGiftCard()->getId()) {
            return $this->getGiftCard()->getCode();
        } else {
            return $subject->getQuote()->getCouponCode();
        }
    }

    protected function getGiftCard()
    {
        $code = $this->sessionFactory->create()->getGiftCode();
        $giftCard = $this->giftCardFactory->create();
        $this->giftCardResource->load($giftCard, $code, 'code');
        return $giftCard;
    }
}
