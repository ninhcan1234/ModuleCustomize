<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mageplaza\GiftCard\Controller\Adminhtml\Code;

class InlineEdit extends \Magento\Backend\App\Action
{

    protected $jsonFactory;
    protected $giftCardRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Mageplaza\GiftCard\Api\GiftCardRepositoryInterface $giftCardRepository
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->giftCardRepository = $giftCardRepository;
    }

    /**
     * Inline edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $date = date('d-m-Y H:i:s');
        $error = false;
        $messages = [];
        
        if ($this->getRequest()->getParam('isAjax')) {
            $giftCardItems = $this->getRequest()->getParam('items', []);
            if (!count($giftCardItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($giftCardItems) as $modelid) {
                    /** @var \Mageplaza\GiftCard\Model\GiftCard $model */
                    $model = $this->giftCardRepository->getById($modelid);
                    try {
                        $model->setData(array_merge($model->getData(), $giftCardItems[$modelid]));
                        $this->giftCardRepository->save($model);
                        $this->messageManager->addSuccessMessage('Save code successfully');
                    } catch (\Exception $e) {
                        $messages[] = "[GiftCard ID: {$modelid}]  {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }
        
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
