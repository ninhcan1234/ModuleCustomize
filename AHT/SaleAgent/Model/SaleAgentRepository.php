<?php
namespace AHT\SaleAgent\Model;

use \AHT\SaleAgent\Api\Data\SaleAgentInterface;

class SaleAgentRepository implements \AHT\SaleAgent\Api\SaleAgentRepositoryInterface{

    protected $_saleAgentResourceModel;
    protected $_saleAgentFactory;
    protected $_collectionFactory;
    protected $_searchResultsFactory;

    public function __construct(
        \AHT\SaleAgent\Model\SaleAgentFactory $saleAgentFactory,
        \AHT\SaleAgent\Model\ResourceModel\SaleAgent $saleAgentResource,
        \AHT\SaleAgent\Model\ResourceModel\SaleAgent\CollectionFactory $collectionFactory,
        \AHT\SaleAgent\Api\Data\SaleAgentSearchResultsInterfaceFactory $searchResultsFactory
    )
    {
        $this->_saleAgentFactory = $saleAgentFactory;
        $this->_saleAgentResourceModel = $saleAgentResource;
        $this->_collectionFactory = $collectionFactory;
        $this->_searchResultsFactory = $searchResultsFactory;
    }

    public function load(SaleAgentInterface $saleAgent, $value, $field= null){
        $this->_saleAgentResourceModel->load($saleAgent, $value, $field = null);
        return $saleAgent;
    }

    public function save(SaleAgentInterface $saleAgent){
        $this->_saleAgentResourceModel->save($saleAgent);
        return $saleAgent;
    }

    public function delete(SaleAgentInterface $saleAgent){
        $this->_saleAgentResourceModel->delete($saleAgent);
        return true;
    }

    public function getById($id){
        $saleAgent = $this->_saleAgentFactory->create();
        $this->_saleAgentResourceModel->load($saleAgent, $id);
        return $saleAgent;
    }

    public function deleteById($id){
        $saleAgent = $this->_saleAgentFactory->create();
        $this->_saleAgentResourceModel->load($saleAgent, $id);
        $this->_saleAgentResourceModel->delete($saleAgent);
        return true;
    }

     /**
     * Load SaleAgent data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \AHT\SaleAgent\Api\Data\SaleAgentSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \AHT\SaleAgent\Model\ResourceModel\SaleAgent\Collection $collection */
        $collection = $this->_collectionFactory->create();
        
        /** @var \AHT\SaleAgent|Api\Data\SaleAgentSearchResultsInterface $searchResults */
        $searchResults = $this->_searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }
    
}
