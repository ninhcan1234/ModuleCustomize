<?php
namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;

use Exception;

class Delete extends \Magento\Backend\App\Action
{

    protected $giftCardFactory;
    protected $giftCardRepository;

    public function __construct(
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Magento\Backend\App\Action\Context $context,
        \Mageplaza\GiftCard\Api\GiftCardRepositoryInterface $giftCardRepository
    )
    {
        $this->giftCardRepository = $giftCardRepository;  
        $this->giftCardFactory = $giftCardFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
  
        if ($id){
            try{
                $this->giftCardRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('Deleted successfully'));
            }catch(Exception $e){
                $this->messageManager->addErrorMessage(__('Unsuccess..'));
            }
        }
    
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index', array('_current' => true));
    }
}