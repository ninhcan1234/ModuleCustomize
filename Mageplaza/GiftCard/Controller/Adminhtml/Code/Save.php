<?php

namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Save CMS page action.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends Action implements HttpPostActionInterface
{

    /**
     * @var GiftCardDataProcessor
     */
    protected $dataProcessor;
    protected $storeManager;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    protected $giftCardFactory;

    /**
     * @var \Mageplaza\GiftCard\Api\GiftCardRepositoryInterface
     */
    protected $giftCardRepository;

    public function __construct(
        Action\Context $context,
        // GiftCardDataProcessor $dataProcessor,
        DataPersistorInterface $dataPersistor,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Mageplaza\GiftCard\Api\GiftCardRepositoryInterface $giftCardRepository,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->giftCardFactory = $giftCardFactory;
        $this->storeManager = $storeManager;
        $this->giftCardRepository = $giftCardRepository;
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        var_dump($data);
        die;
        $date = date('d-m-Y H:i:s');
        $id = $this->getRequest()->getParam('id');
        
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
                    $this->messageManager->addErrorMessage(__('This giftCard no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }
            $model->setData($data);

            try { 
                $this->giftCardRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the giftCard.'));
                return $this->processResultRedirect($model, $resultRedirect, $data);
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
            } catch (\Throwable $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the giftCard.'));
            }

            $this->dataPersistor->set('giftcard', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Process result redirect
     *
     * @param \Mageplaza\GiftCard\Api\Data\PageInterface $model
     * @param \Magento\Backend\Model\View\Result\Redirect $resultRedirect
     * @param array $data
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws LocalizedException
     */
    private function processResultRedirect($model, $resultRedirect, $data)
    {
        if ($this->getRequest()->getParam('back', false) === 'duplicate') {
            $newGiftCard = $this->giftCardFactory->create(['data' => $data]);
            $newGiftCard->setId(null);
            $identifier = $model->getIdentifier() . '-' . uniqid();
            $newGiftCard->setIdentifier($identifier);
            $newGiftCard->setIsActive(false);
            $this->giftCardRepository->save($newGiftCard);
            $this->messageManager->addSuccessMessage(__('You duplicated the gift Card.'));
            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    'id' => $newGiftCard->getId(),
                    '_current' => true
                ]
            );
        }
        $this->dataPersistor->clear('giftcard_giftcard');
        if ($this->getRequest()->getParam('back')) {
            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
