<?php
namespace Mageplaza\GiftCard\Model;

use Mageplaza\GiftCard\Api\Data;
use Mageplaza\GiftCard\Model\ResourceModel\GiftCard as ResourceGiftCard;
use Mageplaza\GiftCard\Api\GiftCardRepositoryInterface;
use Mageplaza\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory as GiftCardCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class GiftCardRepository implements GiftCardRepositoryInterface
{
    /**
     * @var ResourceGiftCard
     */
    protected $resource;

    /**
     * @var GiftCardFactory
     */
    protected $giftCardFactory;

    /**
     * @var GiftCardCollectionFactory
     */
    protected $giftCardCollectionFactory;

    /**
     * @var Data\GiftCardSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Mageplaza\GiftCard\Api\Data\GiftCardInterfaceFactory
     */
    protected $dataGiftCardFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;


    /**
     * @param ResourceGiftCard $resource
     * @param GiftCardFactory $giftCardFactory
     * @param Data\GiftCardInterfaceFactory $dataGiftCardFactory
     * @param GiftCardCollectionFactory $giftCardCollectionFactory
     * @param Data\GiftCardSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceGiftCard $resource,
        GiftCardFactory $giftCardFactory,
        \Mageplaza\GiftCard\Api\Data\GiftCardInterfaceFactory $dataGiftCardFactory,
        GiftCardCollectionFactory $giftCardCollectionFactory,
        Data\GiftCardSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->giftCardFactory = $giftCardFactory;
        $this->giftCardCollectionFactory = $giftCardCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataGiftCardFactory = $dataGiftCardFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Save GiftCard data
     *
     * @param \Mageplaza\GiftCard\Api\Data\GiftCardInterface $giftCard
     * @return GiftCard
     * @throws CouldNotSaveException
     */
    public function save(Data\GiftCardInterface $giftCard)
    {
        try {
            $this->resource->save($giftCard);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $giftCard;
    }

    public function load(Data\GiftCardInterface $giftCard, $value, $field= null){
        $this->resource->load($giftCard, $value, $field = null);
        return $giftCard;
    }

    /**
     * Load GiftCard data by given GiftCard Identity
     *
     * @param string $giftCardId
     * @return GiftCard
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($giftCardId)
    {
        $giftCard = $this->giftCardFactory->create();
        $this->resource->load($giftCard, $giftCardId);
        if (!$giftCard->getId()) {
            throw new NoSuchEntityException(__('The giftCard with the "%1" ID doesn\'t exist.', $giftCardId));
        }
        return $giftCard;
    }

    /**
     * Load GiftCard data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Mageplaza\GiftCard\Api\Data\GiftCardSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \Mageplaza\GiftCard\Model\ResourceModel\GiftCard\Collection $collection */
        $collection = $this->giftCardCollectionFactory->create();

        /** @var Data\GiftCardSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Delete GiftCard
     *
     * @param \Mageplaza\GiftCard\Api\Data\GiftCardInterface $giftCard
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\GiftCardInterface $giftCard)
    {
        try {
            $this->resource->delete($giftCard);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete GiftCard by given GiftCard Identity
     *
     * @param string $giftCardId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($giftCardId)
    {
        return $this->delete($this->getById($giftCardId));
    }

}
