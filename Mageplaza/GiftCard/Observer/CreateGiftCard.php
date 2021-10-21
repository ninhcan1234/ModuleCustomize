<?php

namespace Mageplaza\GiftCard\Observer;

use Exception;

class CreateGiftCard implements \Magento\Framework\Event\ObserverInterface
{
    protected $dataConfig;
    protected $giftCardFactory;
    protected $giftCardRepository;
    protected $giftCardHistoryFactory;
    protected $orderCollectionFactory;
    protected $giftCardHistoryRepository;

    public function __construct(
        \Mageplaza\GiftCard\Helper\Data $dataConfig,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Mageplaza\GiftCard\Api\GiftCardRepositoryInterface $giftCardRepository,
        \Mageplaza\GiftCard\Model\GiftCardHistoryFactory $giftCardHistoryFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Mageplaza\GiftCard\Api\GiftCardHistoryRepositoryInterface $giftCardHistoryRepository
    ) {
        $this->dataConfig = $dataConfig;
        $this->giftCardFactory = $giftCardFactory;
        $this->giftCardRepository = $giftCardRepository;
        $this->giftCardHistoryFactory = $giftCardHistoryFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->giftCardHistoryRepository = $giftCardHistoryRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order =  $observer->getEvent()->getOrder();
        $customerId = $order->getCustomerId();
        $incrementId = $order->getIncrementId();
        $date = date("Y-m-d H:i:s");
        $action = "create";

        foreach ($order->getAllItems() as $item) {
            $productTypeId = $item->getProduct()->getTypeId();
            $hasGiftCardAmount = $item->getProduct()->getGiftcardAmount();
            if ($productTypeId == \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL && $hasGiftCardAmount && $hasGiftCardAmount > 0) {
                $qtyOrder = $item->getQtyOrdered();
                $amountBalance = $item->getProduct()->getGiftcardAmount();
                for ($i = 0; $i < $qtyOrder; $i++) {
                    $code = $this->generateCode();
                    $this->createGiftCard($code, $amountBalance, $incrementId, $date, $customerId, $action);
                }
            }
        }
    }

    private function generateCode()
    {
        $codeLength = $this->dataConfig->getSystemConfig('giftcard/code_config/code_length');
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $stringCode = '';
        for ($i = 0; $i < $codeLength; $i++) {
            $stringCode .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $stringCode;
    }

    protected function createGiftCard($code, $balance, $createFrom, $date, $customerId, $action)
    {
        $giftCard = $this->giftCardFactory->create();
        $giftCard->setCode($code);
        $giftCard->setBalance($balance);
        $giftCard->setCreateFrom($createFrom);
        $giftCard->setCreateAt($date);
        $this->giftCardRepository->save($giftCard);

        $giftCardHistory = $this->giftCardHistoryFactory->create();
        $giftCardHistory->setGiftCardId($giftCard->getId());
        $giftCardHistory->setCustomerId($customerId);
        $giftCardHistory->setAmount($balance);
        $giftCardHistory->setAction($action);
        $giftCardHistory->setActionTime($date);
        $this->giftCardHistoryRepository->save($giftCardHistory);
    }
}
