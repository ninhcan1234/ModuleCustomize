<?php
namespace AHT\SaleAgent\Api;

use AHT\SaleAgent\Api\Data\SaleAgentInterface;

interface SaleAgentRepositoryInterface
{
    /**
    * Retrieve saleagent.
    *
    * @param \AHT\SaleAgent\Api\Data\SaleAgentInterface $saleagentInterface
    * @return \AHT\SaleAgent\Api\Data\SaleAgentInterface
    * @throws \Magento\Framework\Exception\LocalizedException
    */
   public function load(SaleAgentInterface $saleagentInterface, $value, $field = null);

   /**
    * Save saleagent.
    *
    * @param \AHT\SaleAgent\Api\Data\SaleAgentInterface $saleagent
    * @return \AHT\SaleAgent\Api\Data\SaleAgentInterface
    */
   public function save(SaleAgentInterface $saleagent);

   /**
    * Delete saleagent by ID.
    *
    * @param \AHT\SaleAgent\Api\Data\SaleAgentInterface $saleagent
    * @return bool true on success
    */
   public function delete(SaleAgentInterface $saleagent);

   /**
    * Retrieve saleagent.
    *
    * @param int $id
    * @return \AHT\SaleAgent\Api\Data\SaleAgentInterface
    */
   public function getById($id);

   /**
    * Delete saleagent by ID.
    *
    * @param int $id
    * @return bool true on success
    */
   public function deleteById($id);

   // /**
   //  * Retrieve saleagent matching the specified criteria.
   //  *
   //  * @return \AHT\SaleAgent\Api\Data\SaleAgentInterface
   //  */

   // public function getList();

   /**
    * Retrieve blocks matching the specified criteria.
    *
    * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    * @return \AHT\SaleAgent\Api\Data\SaleAgentSearchResultsInterface
    * @throws \Magento\Framework\Exception\LocalizedException
    */
   public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
