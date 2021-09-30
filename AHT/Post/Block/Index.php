<?php
namespace AHT\Post\Block;

use AHT\UiComponent\Helper\GetSystemConfig;
use Magento\Framework\Api\SearchCriteriaBuilder;

class Index extends \Magento\Framework\View\Element\Template
{
    protected $getSystemConfig;
    protected $postRepository;
    protected $searchCriteriaBuilder;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \AHT\Post\Api\PostRepositoryInterface $postRepository,
        array $data = [],
        GetSystemConfig $getSystemConfig,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->postRepository = $postRepository;
        parent::__construct($context, $data);
        $this->getSystemConfig = $getSystemConfig;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function getItemsJson(){
        $results = [];
        foreach($this->getAll() as $post){
            $results[$post->getIdentifier()] = [
                'name' => $post->getName(),
                'description' => $post->getDescription(),
            ];
        }
    }

    public function getAll(){
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $postSearchResults = $this->postRepository->getList($searchCriteria);
        return $postSearchResults->getItems();
    }

    public function toSave(){
        return $this->getUrl('post/action/save');
    }

    public function toEdit($id){
        return $this->getUrl('post/action/edit?id='.$id);
    }

    public function toDelete($id){
        return $this->getUrl('post/action/delete?id='.$id);
    }

    public function getTextContent($path){
        return $this->getSystemConfig->getConfig($path);
    }

}
