<?php

namespace Mageplaza\GiftCard\Controller\Index;

use Exception;

class Delete extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    protected $giftCardFactory;

    protected $giftCardRepository;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Mageplaza\GiftCard\Api\GiftCardRepositoryInterface $giftCardRepository,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory
    ) {
        $this->giftCardRepository = $giftCardRepository;
        $this->giftCardFactory = $giftCardFactory;
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
        $redirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');

        if ($id)
            try {
                $this->giftCardRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('Has deleted successfully'));
                return $redirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(__('Failed!'));
            }

        return $redirect->setPath('*/*/');
    }
}
