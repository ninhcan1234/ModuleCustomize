<?php
namespace AHT\UiComponent\Controller\Adminhtml\Posting;

class Index extends \Magento\Backend\App\Action
{
    CONST TITLE = 'LIST POST';
     /**
     * @var \Magento\Framework\View\Result\PageFactory
     */     
    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        // $resultPage->addBreadcrumb(__('Dashboard'), __('Dashboard'));
        // $resultPage->setActiveMenu('AHT_UiComponent::menu_custom');
        $resultPage->getConfig()->getTitle()->prepend(__(static::TITLE));

        return $resultPage;
    }
}
