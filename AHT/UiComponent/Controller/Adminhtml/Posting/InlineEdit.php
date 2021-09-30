<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AHT\UiComponent\Controller\Adminhtml\Posting;

class InlineEdit extends \Magento\Backend\App\Action
{

    protected $jsonFactory;
    protected $postRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \AHT\Post\Api\PostRepositoryInterface $postRepository
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->postRepository = $postRepository;
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
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $modelid) {
                    /** @var \AHT\Post\Model\Post $model */
                    $model = $this->postRepository->getById($modelid);
                    try {
                        $model->setUpdatedAt($date);
                        $model->setData(array_merge($model->getData(), $postItems[$modelid]));
                        $this->postRepository->save($model);
                    } catch (\Exception $e) {
                        $messages[] = "[Post ID: {$modelid}]  {$e->getMessage()}";
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
