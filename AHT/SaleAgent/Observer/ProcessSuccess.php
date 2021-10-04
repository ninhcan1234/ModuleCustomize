<?php

namespace AHT\SaleAgent\Observer;

class ProcessSuccess implements \Magento\Framework\Event\ObserverInterface
{
    protected $saleAgentFactory;
    protected $saleAgentRepository;
    protected $time;

    public function __construct(
        \AHT\SaleAgent\Model\SaleAgentFactory $saleAgentFactory,
        \AHT\SaleAgent\Api\SaleAgentRepositoryInterface $saleAgentRepository,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    ) {
        $this->saleAgentFactory = $saleAgentFactory;
        $this->saleAgentRepository = $saleAgentRepository;
        $this->time = $date;
    }

    protected function addDataSaleAgent(
        $orderId,
        $orderItemId,
        $sku,
        $price,
        $qtyOrder,
        $commissionType,
        $commissionValue,
        $currentCommissionValue
    ) {
        /** @var \AHT\SaleAgent\Model\SaleAgent $saleAgent */
        $saleAgent = $this->saleAgentFactory->create();
        $saleAgent->setOrderId($orderId);
        $saleAgent->setOrderItemId($orderItemId);
        $saleAgent->setOrderItemSku($sku);
        $saleAgent->setOrderItemPrice($price);
        $saleAgent->setTotalItem($qtyOrder);
        $saleAgent->setCommissionType($commissionType);
        $saleAgent->setCommissionValue($commissionValue);
        $saleAgent->setCurrentCommissionValue($currentCommissionValue);
        $saleAgent->setCreatedAt(date('d-m-Y H:i:s'));
        $this->saleAgentRepository->save($saleAgent);
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order =  $observer->getEvent()->getOrder();
        $orderId = $order->getIncrementId();

        foreach ($order->getAllItems() as $item) {
            $existAgent = $item->getProduct()->getSaleAgentId();
            if ($existAgent) {
                $productId = $item->getProductId();
                $productSku = $item->getSku();
                $qtyOrder = $item->getQtyOrdered();
                $price = $qtyOrder == 1 ? $item->getPrice() : $item->getPrice() * $qtyOrder;
                $commissionType = $item->getProduct()->getCommissionType();
                $commissionValue = $item->getProduct()->getCommissionValue();
                $currentCommissionValue = ($commissionType == 'percent') ? $commissionValue : ($commissionValue * $qtyOrder);
                $this->addDataSaleAgent(
                    $orderId,
                    $productId,
                    $productSku,
                    $price,
                    $qtyOrder,
                    $commissionType,
                    $commissionValue,
                    $currentCommissionValue
                );
            }
        }
    }
}
