<?php
namespace AHT\CheckOut\Model;

use AHT\CheckOut\Api\Data\DeliveryInterface;

class DeliveryRepository implements \AHT\CheckOut\Api\DeliveryRepositoryInterface{

    protected $_deliveryResourceModel;
    protected $_deliveryFactory;
    protected $_collectionFactory;
    protected $_searchResultsFactory;

    public function __construct(
        \AHT\CheckOut\Model\DeliveryFactory $deliveryFactory,
        \AHT\CheckOut\Model\ResourceModel\Delivery $deliveryResource,
        \AHT\CheckOut\Model\ResourceModel\Delivery\CollectionFactory $collectionFactory,
        \AHT\CheckOut\Api\Data\DeliverySearchResultsInterfaceFactory $searchResultsFactory
    )
    {
        $this->_deliveryFactory = $deliveryFactory;
        $this->_deliveryResourceModel = $deliveryResource;
        $this->_collectionFactory = $collectionFactory;
        $this->_searchResultsFactory = $searchResultsFactory;
    }

    public function load(DeliveryInterface $delivery, $value, $field= null){
        $this->_deliveryResourceModel->load($delivery, $value, $field = null);
        return $delivery;
    }

    public function save(DeliveryInterface $delivery){
        $this->_deliveryResourceModel->save($delivery);
        return $delivery;
    }

    public function delete(DeliveryInterface $delivery){
        $this->_deliveryResourceModel->delete($delivery);
        return true;
    }

    public function getById($id){
        $delivery = $this->_deliveryFactory->create();
        $this->_deliveryResourceModel->load($delivery, $id);
        return $delivery;
    }

    public function deleteById($id){
        $delivery = $this->_deliveryFactory->create();
        $this->_deliveryResourceModel->load($delivery, $id);
        $this->_deliveryResourceModel->delete($delivery);
        return true;
    }

     /**
     * Load Delivery data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \AHT\CheckOut\Api\Data\DeliverySearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \AHT\CheckOut\Model\ResourceModel\Delivery\Collection $collection */
        $collection = $this->_collectionFactory->create();
        
        /** @var \AHT\CheckOut|Api\Data\DeliverySearchResultsInterface $searchResults */
        $searchResults = $this->_searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }
    
}
