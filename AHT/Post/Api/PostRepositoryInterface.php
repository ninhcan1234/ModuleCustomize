<?php

namespace AHT\Post\Api;

use AHT\Post\Api\Data\PostInterface;

interface PostRepositoryInterface
{
   /**
    * Retrieve post.
    *
    * @param \AHT\Post\Api\Data\PostInterface $postInterface
    * @return \AHT\Post\Api\Data\PostInterface
    * @throws \Magento\Framework\Exception\LocalizedException
    */
   public function load(PostInterface $postInterface, $value, $field = null);

   /**
    * Save post.
    *
    * @param \AHT\Post\Api\Data\PostInterface $post
    * @return \AHT\Post\Api\Data\PostInterface
    */
   public function save(PostInterface $post);

   /**
    * Delete post by ID.
    *
    * @param \AHT\Post\Api\Data\PostInterface $post
    * @return bool true on success
    */
   public function delete(PostInterface $post);

   /**
    * Retrieve post.
    *
    * @param int $id
    * @return \AHT\Post\Api\Data\PostInterface
    */
   public function getById($id);

   /**
    * Delete post by ID.
    *
    * @param int $id
    * @return bool true on success
    */
   public function deleteById($id);

   // /**
   //  * Retrieve post matching the specified criteria.
   //  *
   //  * @return \AHT\Post\Api\Data\PostInterface
   //  */

   // public function getList();

   /**
    * Retrieve blocks matching the specified criteria.
    *
    * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    * @return \AHT\Post\Api\Data\PostSearchResultsInterface
    * @throws \Magento\Framework\Exception\LocalizedException
    */
   public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

}
