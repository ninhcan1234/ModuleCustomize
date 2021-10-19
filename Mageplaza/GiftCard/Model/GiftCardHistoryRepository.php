<?php
namespace Mageplaza\GiftCard\Model;

use Mageplaza\GiftCard\Api\Data;
use Mageplaza\GiftCard\Model\ResourceModel\GiftCardHistory as ResourceGiftCardHistory;
use Mageplaza\GiftCard\Api\GiftCardHistoryRepositoryInterface;
use Mageplaza\GiftCard\Model\ResourceModel\GiftCardHistory\CollectionFactory as GiftCardHistoryCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

class GiftCardHistoryRepository implements GiftCardHistoryRepositoryInterface
{
    /**
     * @var ResourceGiftCardHistory
     */
    protected $resource;

    /**
     * @var GiftCardHistoryFactory
     */
    protected $giftCardHistoryFactory;

    /**
     * @var GiftCardHistoryCollectionFactory
     */
    protected $giftCardHistoryCollectionFactory;

    /**
     * @var Data\GiftCardHistorySearchResultsInterfaceFactory
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
     * @var \Mageplaza\GiftCard\Api\Data\GiftCardHistoryInterfaceFactory
     */
    protected $dataGiftCardHistoryFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;


    /**
     * @param ResourceGiftCardHistory $resource
     * @param GiftCardHistoryFactory $giftCardHistoryFactory
     * @param Data\GiftCardHistoryInterfaceFactory $dataGiftCardHistoryFactory
     * @param GiftCardHistoryCollectionFactory $giftCardHistoryCollectionFactory
     * @param Data\GiftCardHistorySearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceGiftCardHistory $resource,
        GiftCardHistoryFactory $giftCardHistoryFactory,
        \Mageplaza\GiftCard\Api\Data\GiftCardHistoryInterfaceFactory $dataGiftCardHistoryFactory,
        GiftCardHistoryCollectionFactory $giftCardHistoryCollectionFactory,
        Data\GiftCardHistorySearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->giftCardHistoryFactory = $giftCardHistoryFactory;
        $this->giftCardHistoryCollectionFactory = $giftCardHistoryCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataGiftCardHistoryFactory = $dataGiftCardHistoryFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * Save GiftCardHistory data
     *
     * @param \Mageplaza\GiftCard\Api\Data\GiftCardHistoryInterface $giftCardHistory
     * @return GiftCardHistory
     * @throws CouldNotSaveException
     */
    public function save(Data\GiftCardHistoryInterface $giftCardHistory)
    {
        try {
            $this->resource->save($giftCardHistory);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $giftCardHistory;
    }

    public function load(Data\GiftCardHistoryInterface $giftCardHistory, $value, $field= null){
        $this->resource->load($giftCardHistory, $value, $field = null);
        return $giftCardHistory;
    }

    /**
     * Load GiftCardHistory data by given GiftCardHistory Identity
     *
     * @param string $giftCardHistoryId
     * @return GiftCardHistory
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($giftCardHistoryId)
    {
        $giftCardHistory = $this->giftCardHistoryFactory->create();
        $this->resource->load($giftCardHistory, $giftCardHistoryId);
        if (!$giftCardHistory->getId()) {
            throw new NoSuchEntityException(__('The giftCardHistory with the "%1" ID doesn\'t exist.', $giftCardHistoryId));
        }
        return $giftCardHistory;
    }

    /**
     * Load GiftCardHistory data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Mageplaza\GiftCard\Api\Data\GiftCardHistorySearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \Mageplaza\GiftCard\Model\ResourceModel\GiftCardHistory\Collection $collection */
        $collection = $this->giftCardHistoryCollectionFactory->create();

        /** @var Data\GiftCardHistorySearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Delete GiftCardHistory
     *
     * @param \Mageplaza\GiftCard\Api\Data\GiftCardHistoryInterface $giftCardHistory
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\GiftCardHistoryInterface $giftCardHistory)
    {
        try {
            $this->resource->delete($giftCardHistory);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete GiftCardHistory by given GiftCardHistory Identity
     *
     * @param string $giftCardHistoryId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($giftCardHistoryId)
    {
        return $this->delete($this->getById($giftCardHistoryId));
    }

}
