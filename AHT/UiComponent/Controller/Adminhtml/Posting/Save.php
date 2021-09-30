<?php

namespace AHT\UiComponent\Controller\Adminhtml\Posting;

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
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */


    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;
    protected $storeManager;
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Magento\Cms\Model\PostFactory
     */
    private $postFactory;

    /**
     * @var \Magento\Cms\Api\PostRepositoryInterface
     */
    private $postRepository;
    protected $imageUploader;
    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     * @param DataPersistorInterface $dataPersistor
     * @param \Magento\Cms\Model\PageFactory|null $pageFactory
     * @param \Magento\Cms\Api\PageRepositoryInterface|null $pageRepository
     */
    public function __construct(
        Action\Context $context,
        // PostDataProcessor $dataProcessor,
        DataPersistorInterface $dataPersistor,
        \AHT\Post\Model\PostFactory $postFactory,
        \AHT\Post\Api\PostRepositoryInterface $postRepository,
        \AHT\Post\Model\ImageUploader $imageUploader,
        StoreManagerInterface $storeManager
    ) {
        // $this->dataProcessor = $dataProcessor;
        $this->dataPersistor = $dataPersistor;
        $this->postFactory = $postFactory;
        $this->postRepository = $postRepository;
        $this->imageUploader = $imageUploader;
        parent::__construct($context);
        $this->storeManager = $storeManager;
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

        if(isset($data['images'])){
            $imageName= $data['images'][0]['name'];
            $imageUrl = $data['images'][0]['url'];
        }

        $date = date('d-m-Y H:i:s');
        $id = $this->getRequest()->getParam('id');
        
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if (empty($id)) {
                $data['post_id'] = null;
                $data['created_at'] = $date;
            } else {
                $data['updated_at'] = $date;
            }

            $model = $this->postFactory->create();

            if ($id) {
                try {
                    $model = $this->postRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This post no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }
            isset($data['images']) ? $data['image'] = $imageName: $data['image'] = null;
            $model->setData($data);

            try { 
                $this->postRepository->save($model);
                $this->imageUploader->moveFileFromTmp($imageName);
                $this->messageManager->addSuccessMessage(__('You saved the post.'));
                return $this->processResultRedirect($model, $resultRedirect, $data);
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
            } catch (\Throwable $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the post.'));
            }

            $this->dataPersistor->set('post', $data);
            return $resultRedirect->setPath('*/*/edit', ['post_id' => $this->getRequest()->getParam('post_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Process result redirect
     *
     * @param \Magento\Cms\Api\Data\PageInterface $model
     * @param \Magento\Backend\Model\View\Result\Redirect $resultRedirect
     * @param array $data
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws LocalizedException
     */
    private function processResultRedirect($model, $resultRedirect, $data)
    {
        if ($this->getRequest()->getParam('back', false) === 'duplicate') {
            $newPost = $this->postFactory->create(['data' => $data]);
            $newPost->setId(null);
            $identifier = $model->getIdentifier() . '-' . uniqid();
            $newPost->setIdentifier($identifier);
            $newPost->setIsActive(false);
            $this->postRepository->save($newPost);
            $this->messageManager->addSuccessMessage(__('You duplicated the post.'));
            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    'post_id' => $newPost->getId(),
                    '_current' => true
                ]
            );
        }
        $this->dataPersistor->clear('cms_post');
        if ($this->getRequest()->getParam('back')) {
            return $resultRedirect->setPath('*/*/edit', ['post_id' => $model->getId(), '_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
