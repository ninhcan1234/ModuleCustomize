<?php
namespace AHT\Post\Block;

class Edit extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    protected $_postFactory;
    protected $_postRepositoryInterface;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \AHT\Post\Api\PostRepositoryInterface $postRepositoryInterface,
        \AHT\Post\Model\PostFactory $postFactory,
        array $data = []
    ) {
        $this->_postRepositoryInterface = $postRepositoryInterface;
        $this->_postFactory= $postFactory;
        parent::__construct($context, $data);
    }

    public function getTitle()
    {
        return __('Edit Post');
    }

    public function getObjectById(){
        $param = $this->getRequest()->getParam('id');
        $id = substr($param, 0, -1);
        $object = $this->_postFactory->create();
        $data = $this->_postRepositoryInterface->load($object, $id);
        return $data;     
    }

    public function toSave(){
        return $this->getUrl('post/action/save');
    }

   
}
