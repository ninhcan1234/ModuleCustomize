<?php

namespace AHT\CheckOut\Block\Adminhtml\Order\Delivery;

class Form extends \Magento\Sales\Block\Adminhtml\Order\Create\Form\AbstractForm
{
    /**
     * Address form template
     */
    protected $_template = 'AHT_CheckOut::delivery_edit.phtml';

    /**
     * Core registy
     * 
     * @var \Magento\Framework\Registry
     */
    protected $coregistry;
    protected $deliveryFactory;
    protected $orderRepository;
    protected $deliveryResource;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate,
     * @param \Magento\Framework\Data\FormFactory $formFactory,
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
     * @param \Magento\Framework\Registry $registry,
     * @param \AHT\CheckOut\Model\DeliveryFactory $deliveryFactory,
     * @param \AHT\CheckOut\Api\DeliveryRepositoryInterface $deliveryRepository,
     * 
     */

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Registry $registry,
        \AHT\CheckOut\Model\DeliveryFactory $deliveryFactory,
        \AHT\CheckOut\Model\ResourceModel\Delivery $deliveryResource,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->orderRepository = $orderRepository;
        $this->deliveryFactory = $deliveryFactory;
        $this->deliveryResource = $deliveryResource;
        parent::__construct(
            $context,
            $sessionQuote,
            $orderCreate,
            $priceCurrency,
            $formFactory,
            $dataObjectProcessor,
            $data
        );
    }

    protected function _prepareForm()
    {
        $fieldset = $this->_form->addFieldset('main', ['no_container'=> true]);
        $fieldset->addField(
            'quote_id',
            'hidden',
            [
                'name'=>'quote_id',
                'label'=>__(''),
                'value'=> $this->getDelivery()->getQuoteId()
            ]
        );
        $fieldset->addField(
            'delivery_date',
            'hidden',
            [
                'name'=>'delivery_date',
                'label'=>__('Delivery date'),
                'date_format'=> 'yyyy-MM-dd'
            ]
        );
        $fieldset->addField(
            'delivery_comment',
            'textarea',
            [
                'name'=>'delivery_comment',
                'label'=>__('Delivery comment'),
                'value'=> $this->getDelivery()->getDeliveryComment(),
                'row' => 10,
                'disabled' => 'disabled'
            ]
        );
        $this->_form->setId('edit_form');
        $this->_form->setMethod('post');
        $this->_form->setAction( $this->getUrl('sales/delivery/save', ['order_id' =>$this->getOrderId()]) );
        $this->_form->setUseContainer(true);

        return $this;
    }

    public function getOrderId()
    {
        return $this->getRequest()->getParam('order_id');
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
    
}
