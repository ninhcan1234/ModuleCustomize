<?php
namespace AHT\UiComponent\Controller\Adminhtml\Posting;

use Exception;

class Delete extends \Magento\Backend\App\Action
{

    protected $_postFactory;
    protected $_postRepository;

    public function __construct(
        \AHT\Post\Model\PostFactory $postFactory,
        \Magento\Backend\App\Action\Context $context,
        \AHT\Post\Api\PostRepositoryInterface $postRepositoryInterface
    )
    {
        $this->_postRepository = $postRepositoryInterface;  
        $this->_postFactory = $postFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
  
        if ($id){
            try{
                $this->_postRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('Deleted successfully'));
            }catch(Exception $e){
                $this->messageManager->addErrorMessage(__('Unsuccess..'));
            }
        }
    
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index', array('_current' => true));
    }
}