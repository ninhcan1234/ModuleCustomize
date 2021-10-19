<?php
namespace Mageplaza\GiftCard\Api;

interface GiftCardHistoryRepositoryInterface
{
    /**
    * Retrieve gift card.
    *
    * @param \Mageplaza\GiftCard\Api\Data\GiftCardHistoryInterface $giftCardHistory
    * @return \Mageplaza\GiftCard\Api\Data\GiftCardHistoryInterface
    * @throws \Magento\Framework\Exception\LocalizedException
    */
   public function load(Data\GiftCardHistoryInterface $giftCardHistory, $value, $field = null);

   /**
    * Save giftCardHistory.
    *
    * @param \Mageplaza\GiftCard\Api\Data\GiftCardHistoryInterface $giftCardHistory
    * @return \Mageplaza\GiftCard\Api\Data\GiftCardHistoryInterface
    * @throws \Magento\Framework\Exception\LocalizedException
    */
   public function save(Data\GiftCardHistoryInterface $giftCardHistory);

   /**
    * Retrieve giftCardHistory.
    *
    * @param int $giftCardHistoryId
    * @return \Mageplaza\GiftCard\Api\Data\GiftCardHistoryInterface
    * @throws \Magento\Framework\Exception\LocalizedException
    */
   public function getById($giftCardHistoryId);

   /**
    * Retrieve giftCardHistorys matching the specified criteria.
    *
    * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    * @return \Mageplaza\GiftCard\Api\Data\GiftCardSearchResultsInterface
    * @throws \Magento\Framework\Exception\LocalizedException
    */
   public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

   /**
    * Delete giftCardHistory.
    *
    * @param \Mageplaza\GiftCard\Api\Data\GiftCardHistoryInterface $giftCardHistory
    * @return bool true on success
    * @throws \Magento\Framework\Exception\LocalizedException
    */
   public function delete(Data\GiftCardHistoryInterface $giftCardHistory);

   /**
    * Delete giftCardHistory by ID.
    *
    * @param int $giftCardHistoryId
    * @return bool true on success
    * @throws \Magento\Framework\Exception\NoSuchEntityException
    * @throws \Magento\Framework\Exception\LocalizedException
    */
   public function deleteById($giftCardHistoryId);
}
