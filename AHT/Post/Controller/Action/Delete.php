<?php
namespace AHT\Post\Controller\Action;
use Exception;

class Delete extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;
    protected $_postFactory;
    protected $_postResourceModel;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
       \Magento\Framework\App\Action\Context $context,
       \Magento\Framework\View\Result\PageFactory $pageFactory,
       \AHT\Post\Api\PostRepositoryInterface $postResourceModel
    )
    {
        $this->_postResourceModel = $postResourceModel;
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }
    /**
     * View page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $param = $this->getRequest()->getParam('id');
        $id = substr($param, 0, -1);
        if($id)
            try{
                $this->_postResourceModel->deleteById($id);
                $this->messageManager->addSuccessMessage(__('Has deleted successfully')); 
            }catch(Exception $e){
                $this->messageManager->addErrorMessage(__('Failed!'));
            }
        

        $redirect = $this->resultRedirectFactory->create();
        return $redirect->setPath('*/index/');
    }
}
