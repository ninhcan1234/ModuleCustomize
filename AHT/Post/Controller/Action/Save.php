<?php
namespace AHT\Post\Controller\Action;

use Exception;

class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    protected $_post;
    protected $_postResource;

    public function __construct(
       \Magento\Framework\App\Action\Context $context,
       \Magento\Framework\View\Result\PageFactory $pageFactory,
       \AHT\Post\Model\PostFactory $postFactory,
       \AHT\Post\Model\PostRepository $postRepository
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->_post = $postFactory;
        $this->_postResource = $postRepository;
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
        $id = $this->getRequest()->getParam('post_id');
        $postInterface = $this->_post->create();
        $date = date('d-m-Y H:i:s');
        
        if(empty($id))
            try{
                $data['status']= 0;
                $data['created_at'] = $date;
                $model = $postInterface->setData($data);
                $this->_postResource->save($model);
                $this->messageManager->addSuccessMessage(__("The post has been saved %1", $data['name']));
            } catch(Exception $e) {
                $this->messageManager->addErrorMessage(__("Unsuccess, Have some errors!!"));
            }
        else
            try{
                $postInterface->setId($id);
                $postInterface->setName($data['name']);
                $postInterface->setDescription($data['description']);
                $postInterface->setUpdatedAt($date);
                $postInterface->setStatus(0);
                $this->_postResource->save($postInterface);
                $this->messageManager->addSuccessMessage(__("The post id " .$id. "has been updated"));
            }catch(Exception $e){
                $this->messageManager->addErrorMessage(__("Failed!!"));
            }

       
    }
}
