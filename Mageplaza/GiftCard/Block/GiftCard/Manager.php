<?php

namespace Mageplaza\GiftCard\Block\GiftCard;

class Manager extends \Magento\Framework\View\Element\Template
{

    /**
     * @var string
     */
    protected $_template = 'Mageplaza_GiftCard::mageplaza.phtml';

    protected $helperConfig;
    protected $customerSessionFactory;
    protected $priceHelper;
    protected $resourceConnection;

    /**
     * @var \Mageplaza\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory
     */
    protected $giftCardCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Mageplaza\GiftCard\Model\ResourceModel\GiftCard\Collection
     */
    protected $giftCards;

    /**
     * @var CollectionFactoryInterface
     */
    private $orderCollectionFactory;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mageplaza\GiftCard\Helper\Data $helperConfig,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Mageplaza\GiftCard\Model\ResourceModel\GiftCard\CollectionFactory $giftCardCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        array $data = []
    ) {
        $this->giftCardCollectionFactory = $giftCardCollectionFactory;
        $this->customerSessionFactory = $customerSessionFactory;
        $this->resourceConnection = $resourceConnection;
        $this->helperConfig = $helperConfig;
        $this->priceHelper  = $priceHelper;
        parent::__construct($context, $data);
    }

    public function getBalanceCurrentUser()
    {
        $currentSession = $this->customerSessionFactory->create();
        return $currentSession->getCustomer()->getGiftcardBalance();
    }

    public function formatPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }

    public function getCollectionHistory()
    {
        $currentSession = $this->customerSessionFactory->create();
        $customerId = $currentSession->getCustomerId();

        if (!($customerId )) {
            return false;
        }

        $page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 4;   
        $connection = $this->resourceConnection->getConnection();
        $giftCardHistory = $connection->getTableName('giftcard_history');
        $customerEntity = $connection->getTableName('customer_entity');
        $collection = $this->giftCardCollectionFactory->create();
        $collection->addFieldToFilter('customer_id', $customerId);
        $collection->getSelect()
            ->joinRight(
                ['gch' => $giftCardHistory],
                'main_table.giftcard_id = gch.giftcard_id',
                ['action_time', 'amount', 'action']
            )->joinLeft(
                ['ce' => $customerEntity],
                'gch.customer_id = ce.entity_id'
            );
        $collection->setOrder('amount','DESC');
        $collection->setOrder('history_id','DESC');
        $collection->setCurPage($page);
        $collection->setPageSize($pageSize);
        return $collection;
    }

    protected function _prepareLayout()
    {
       parent::_prepareLayout();
       $this->pageConfig->getTitle()->set(__('Gift Card Information'));
    
       if ($this->getCollectionHistory()) {
          $blockName = 'gift.card.history.pager';
          $pager = null;
          if ($this->getLayout()->getBlock($blockName)) {
              $pager = $this->getLayout()->getBlock($blockName)
                ->setAvailableLimit(array(4=>4, 8=>8, 12=>12, 16=>16))
                ->setShowPerPage(true)->setCollection(
                  $this->getCollectionHistory()
                );
          } else {
              $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                $blockName
              )->setAvailableLimit(array(4=>4, 8=>8, 12=>12, 16=>16))
              ->setShowPerPage(true)->setCollection(
                $this->getCollectionHistory()
              );
          }
          $this->setChild('pager', $pager);
          $this->getCollectionHistory()->load();
       }
       return $this;
    }

    /**
     * Get Pager child block output
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get order track URL
     *
     * @param object $order
     * @return string
     * @deprecated 102.0.3 Action does not exist
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getTrackUrl($order)
    {
        //phpcs:ignore Magento2.Functions.DiscouragedFunction
        trigger_error('Method is deprecated', E_USER_DEPRECATED);
        return '';
    }

    /**
     * Get customer account URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }
}
