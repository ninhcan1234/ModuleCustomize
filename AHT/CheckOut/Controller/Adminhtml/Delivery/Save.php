<?php
namespace AHT\CheckOut\Controller\Adminhtml\Delivery;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Magento_Sales::actions_edit';

    const PAGE_TITLE = 'Page Title';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;
    protected $deliveryFactory;
    protected $deliveryRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
       \Magento\Backend\App\Action\Context $context,
       \Magento\Framework\View\Result\PageFactory $pageFactory,
       \AHT\CheckOut\Model\DeliveryFactory $deliveryFactory,
       \AHT\CheckOut\Api\DeliveryRepositoryInterface $deliveryRepository
    )
    {
        $this->deliveryRepository = $deliveryRepository;
        $this->deliveryFactory = $deliveryFactory;
        $this->pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $data = $this->getRequest()->getParams();
        $resultRedirect = $this->resultRedirectFactory->create();
        try{
            $delivery = $this->deliveryFactory->create();
            $this->deliveryRepository->load($delivery, $id);//
            $delivery->setDeliveryDate($data['delivery_date']);
            $this->deliveryRepository->save($delivery);
            $this->messageManager->addSuccessMessage(__('Delivery date has been save successfully!'));
            return $resultRedirect->setPath('*/*/');
        }catch(LocalizedException $e){
            $this->messageManager->addErrorMessage($e->getMessage());
        }catch(\Exception $e){
            $this->messageManager->addExceptionMessage($e, __("Can\'t save the delivery now."));
        }
    }

    /**
     * Is the user allowed to view the page.
    *
    * @return bool
    */
    // protected function _isAllowed()
    // {
    //     return $this->_authorization->isAllowed(static::ADMIN_RESOURCE);
    // }
}
