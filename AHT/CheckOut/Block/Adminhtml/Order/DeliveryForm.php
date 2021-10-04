<?php
namespace AHT\CheckOut\Block\Adminhtml\Order;

class DeliveryForm extends \Magento\Backend\Block\Widget\Form\Container
{
    protected $order;
    protected $formFactory;
    protected $_form;
    protected $coreRegistry = null;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        $this->order = $order;
        $this->formFactory = $formFactory;
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_controller = 'adminhtml_delivery';
        $this->_mode = 'edit';
        $this->_blockGroup = 'AHT_CheckOut';
        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->remove('delete');
    }

    public function getOrderId()
    {
        return $this->getRequest()->getParam('order_id');
    }

    public function getTitle()
    {
        return __('Delivery Information');
    }

    public function getBackUrl()
    {
        return $this->getUrl('sale/*/view', ['order_id' => $this->getOrderId()]);
    }
}
