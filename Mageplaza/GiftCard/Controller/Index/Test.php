<?php

namespace Mageplaza\GiftCard\Controller\Index;

use Magento\Framework\Exception\LocalizedException;

class Test extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Mageplaza\GiftCard\Model\GiftCardFactory
     */
    protected $giftCardFactory;

    /**
     * @var \Mageplaza\GiftCard\Api\GiftCardRepositoryInterface
     */
    protected $giftCardRepository;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Mageplaza\GiftCard\Api\GiftCardRepositoryInterface $giftCardRepository
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
        $data = $this->getRequest()->getParams();
        $date = date('d-m-Y H:i:s');
        $id = $this->getRequest()->getParam('giftcard_id');

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            if (empty($id)) {
                $data['giftcard_id'] = null;
                $data['create_at'] = $date;
            }

            $model = $this->giftCardFactory->create();

            if ($id) {
                try {
                    $model = $this->giftCardRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This Gift Card no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }
            // $model->setBalance($data['balance']);
            // $model->setBalance($data['balance']);
            $model->setData($data);

            try {
                $this->giftCardRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the Gift Card.'));
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
            } catch (\Throwable $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the giftCard.'));
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
