<?php
namespace Mageplaza\GiftCard\Api;

/**
 * CMS giftCard CRUD interface.
 * @api
 * @since 100.0.2
 */
interface GiftCardRepositoryInterface
{
    /**
    * Retrieve gift card.
    *
    * @param \Mageplaza\GiftCard\Api\Data\GiftCardInterface $giftCardInterface
    * @return \Mageplaza\GiftCard\Api\Data\GiftCardInterface
    * @throws \Magento\Framework\Exception\LocalizedException
    */
   public function load(Data\GiftCardInterface $giftCardInterface, $value, $field = null);

    /**
     * Save giftCard.
     *
     * @param \Mageplaza\GiftCard\Api\Data\GiftCardInterface $giftCard
     * @return \Mageplaza\GiftCard\Api\Data\GiftCardInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\GiftCardInterface $giftCard);

    /**
     * Retrieve giftCard.
     *
     * @param int $giftCardId
     * @return \Mageplaza\GiftCard\Api\Data\GiftCardInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($giftCardId);

    /**
     * Retrieve giftCards matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Mageplaza\GiftCard\Api\Data\GiftCardSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete giftCard.
     *
     * @param \Mageplaza\GiftCard\Api\Data\GiftCardInterface $giftCard
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\GiftCardInterface $giftCard);

    /**
     * Delete giftCard by ID.
     *
     * @param int $giftCardId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($giftCardId);
}
