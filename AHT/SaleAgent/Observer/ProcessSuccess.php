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

    protected function addDataSaleAgent($orderId, $orderItemId, $sku, $price, $commissionType, $commissionValue)
    {
        /** @var \AHT\SaleAgent\Model\SaleAgent $saleAgent */
        $saleAgent = $this->saleAgentFactory->create();
        $saleAgent->setOrderId($orderId);
        $saleAgent->setOrderItemId($orderItemId);
        $saleAgent->setOrderItemSku($sku);
        $saleAgent->setOrderItemPrice($price);
        $saleAgent->setCommissionType($commissionType);
        $saleAgent->setCommissionValue($commissionValue);
        $saleAgent->setCreatedAt($this->time);
        $this->saleAgentRepository->save($saleAgent);
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order =  $observer->getEvent()->getOrder();
        $orderId = $order->getIncrementId();

        foreach ($order->getAllItems() as $item) {
            $productId = $item->getProductId();  //Product <=> Item
            $productSku = $item->getSku();
            $price = $item->getPrice();
            $commissionType = $item->getProduct()->getCommissionType();
            $commissionValue = $item->getProduct()->getCommissionValue();
        }
        $this->addDataSaleAgent($orderId, $productId, $productSku, $price, $commissionType, $commissionValue);
    }

}
