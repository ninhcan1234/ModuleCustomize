<?php
namespace AHT\UiComponent\Controller\Adminhtml\Attrcustomer;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Backend\App\Action;

/**
 * Edit CMS page action.
 */
class Edit extends Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    protected $_attributeResourceModel;
    protected $_attributeFactory;
    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\ResourceModel\Attribute $attributeResourceModel,
        \Magento\Customer\Model\AttributeFactory $attributeFactory
    ) {
        $this->_attributeResourceModel = $attributeResourceModel;
        $this->resultPageFactory = $resultPageFactory;
        $this->_attributeFactory = $attributeFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('AHT_Post::custom_menu')
            ->addBreadcrumb(__('Attributes'), __('Attributes'))
            ->addBreadcrumb(__('Manage Attribute'), __('Manage Attribute'));
        return $resultPage;
    }

    /**
     * Edit CMS page
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID and create model
        $attributeModel = $this->_attributeFactory->create();
        $id = $this->getRequest()->getParam('id');

        // 2. Initial checking
        if ($id) {
            $this->_attributeResourceModel->load($attributeModel, $id);
            if (!$attributeModel->getId()) {
                $this->messageManager->addErrorMessage(__('This page no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->_coreRegistry->register('magento_customer_attribute', $attributeModel);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Update Attribute') : __('New Attribute'),
            $id ? __('Update Attribute') : __('New Attribute')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Attributes'));
        $resultPage->getConfig()->getTitle()
            ->prepend($attributeModel->getId() ? 'Code: '.$attributeModel->getName() : __('New Attribute'));

        return $resultPage;
    }
}
