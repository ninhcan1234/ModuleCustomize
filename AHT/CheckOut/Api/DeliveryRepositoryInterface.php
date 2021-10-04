<?php
namespace AHT\CheckOut\Api;

use AHT\CheckOut\Api\Data\DeliveryInterface;

interface DeliveryRepositoryInterface
{
   /**
    * Retrieve delivery.
    *
    * @param \AHT\CheckOut\Api\Data\DeliveryInterface $deliveryInterface
    * @return \AHT\CheckOut\Api\Data\DeliveryInterface
    * @throws \Magento\Framework\Exception\LocalizedException
    */
    public function load(DeliveryInterface $deliveryInterface, $value, $field = null);

    /**
     * Save delivery.
     *
     * @param \AHT\CheckOut\Api\Data\DeliveryInterface $delivery
     * @return \AHT\CheckOut\Api\Data\DeliveryInterface
     */
    public function save(DeliveryInterface $delivery);
 
    /**
     * Delete delivery by ID.
     *
     * @param \AHT\CheckOut\Api\Data\DeliveryInterface $delivery
     * @return bool true on success
     */
    public function delete(DeliveryInterface $delivery);
 
    /**
     * Retrieve delivery.
     *
     * @param int $id
     * @return \AHT\CheckOut\Api\Data\DeliveryInterface
     */
    public function getById($id);
 
    /**
     * Delete delivery by ID.
     *
     * @param int $id
     * @return bool true on success
     */
    public function deleteById($id);
 
    // /**
    //  * Retrieve delivery matching the specified criteria.
    //  *
    //  * @return \AHT\CheckOut\Api\Data\DeliveryInterface
    //  */
 
    // public function getList();
 
    /**
     * Retrieve blocks matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \AHT\CheckOut\Api\Data\CheckOutSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
