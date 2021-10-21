<?php

namespace Mageplaza\GiftCard\Observer;

class OrderWithGiftCard implements \Magento\Framework\Event\ObserverInterface
{
    protected $giftCardHistoryResource;
    protected $giftCardHistoryFactory;
    protected $giftCardResource;
    protected $giftCardFactory;
    protected $checkoutSession;

    public function __construct(
        \Mageplaza\GiftCard\Model\ResourceModel\GiftCardHistory $giftCardHistoryResource,
        \Mageplaza\GiftCard\Model\ResourceModel\GiftCard $giftCardResource,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Mageplaza\GiftCard\Model\GiftCardHistoryFactory $giftCardHistoryFactory,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->giftCardHistoryResource = $giftCardHistoryResource;
        $this->giftCardHistoryFactory = $giftCardHistoryFactory;
        $this->giftCardResource = $giftCardResource;
        $this->giftCardFactory = $giftCardFactory;
        $this->checkoutSession = $checkoutSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $incrementId = $order->getIncrementId();
        $id = $this->checkoutSession->getGiftCodeId();

        if ($id) {
            $giftCardHistory = $this->giftCardHistoryFactory->create();
            $this->giftCardHistoryResource->load($giftCardHistory, $id, 'giftcard_id');
            $giftCardHistory->setAction('used for ' . $incrementId);
            $giftCardHistory->setAmount(0);
            $this->giftCardHistoryResource->save($giftCardHistory);
            $this->checkoutSession->unsGiftCodeId();
            $this->checkoutSession->unsGiftCode();
        }
    }
}
