<?php
namespace AHT\Post\Model;

use \AHT\Post\Api\Data\PostInterface;

class PostRepository implements \AHT\Post\Api\PostRepositoryInterface{

    protected $_postResourceModel;
    protected $_postFactory;
    protected $_collectionFactory;
    protected $_searchResultsFactory;

    public function __construct(
        \AHT\Post\Model\PostFactory $postFactory,
        \AHT\Post\Model\ResourceModel\Post $postResource,
        \AHT\Post\Model\ResourceModel\Post\CollectionFactory $collectionFactory,
        \AHT\Post\Api\Data\PostSearchResultsInterfaceFactory $searchResultsFactory
    )
    {
        $this->_postFactory = $postFactory;
        $this->_postResourceModel = $postResource;
        $this->_collectionFactory = $collectionFactory;
        $this->_searchResultsFactory = $searchResultsFactory;
    }

    public function load(PostInterface $post, $value, $field= null){
        $this->_postResourceModel->load($post, $value, $field = null);
        return $post;
    }

    public function save(PostInterface $post){
        $this->_postResourceModel->save($post);
        return $post;
    }

    public function delete(PostInterface $post){
        $this->_postResourceModel->delete($post);
        return true;
    }

    public function getById($id){
        $post = $this->_postFactory->create();
        $this->_postResourceModel->load($post, $id);
        return $post;
    }

    public function deleteById($id){
        $post = $this->_postFactory->create();
        $this->_postResourceModel->load($post, $id);
        $this->_postResourceModel->delete($post);
        return true;
    }

     /**
     * Load Post data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \AHT\Post\Api\Data\PostSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \AHT\Post\Model\ResourceModel\Post\Collection $collection */
        $collection = $this->_collectionFactory->create();
        
        /** @var \AHT\Post|Api\Data\PostSearchResultsInterface $searchResults */
        $searchResults = $this->_searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }
    
}
