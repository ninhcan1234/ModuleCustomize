<?php
namespace AHT\Post\Model\ResourceModel;

class Post extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('my_post', 'id');
    }

    
    // protected function _afterSave(\Magento\Framework\Model\AbstractModel $object){
    //     $image = $object->getData('image');
    //     if($image !=null){
    //         $imageUploader = \Magento\Framework\App\ObjectManager::getInstance()->get('AHT\Post\Model\ImageUploader');
    //         $imageUploader->moveFileFromTmp($image);
    //     }
    //     return $this;
    // }
}
