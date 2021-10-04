<?php
namespace AHT\CheckOut\Block\Adminhtml\Order;

class Delivery extends \Magento\Framework\View\Element\Template
{
    protected $orderRepository;
    protected $checkoutSession;
    protected $deliveryFactory;
    protected $deliveryResource;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \AHT\CheckOut\Model\DeliveryFactory $deliveryFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \AHT\CheckOut\Model\ResourceModel\Delivery $deliveryResource,
        array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        $this->deliveryFactory = $deliveryFactory;
        $this->checkoutSession = $checkoutSession;
        $this->deliveryResource = $deliveryResource;
        parent::__construct($context, $data);
    }

    public function getDelivery()
    {
        $orderId = $this->getOrderId();
        $order = $this->orderRepository->get($orderId);
        $quoteId = $order->getQuoteId(); 
        $delivery = $this->deliveryFactory->create();
        $this->deliveryResource->load($delivery, $quoteId,'quote_id');
        return $delivery;
    }

    public function getOrderId()
    {
        return $this->getRequest()->getParam('order_id');
    }

    public function getEditLink($label = ''){
        if(empty($label)){
            $label = __('Edit');
        }

        $url = $this->getUrl('sales/delivery/edit', ['order_id' => $this->getOrderId()]);
        return '<a href="' . $this->escapeUrl($url) . '">' . $this->escapeHtml($label) . '</a>';
    }


}
