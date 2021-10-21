<?php

namespace Mageplaza\GiftCard\Controller\GiftCard;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\PageCache\Version;

class Redeem extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;
    protected $giftCardFactory;
    protected $giftCardRepository;
    protected $giftCardResource;
    protected $currentSession;
    protected $resourceConnection;
    protected $giftCardHistoryResource;
    protected $giftCardHistoryFactory;
    protected $cacheTypeList;
    protected $cacheFrontendPool;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Mageplaza\GiftCard\Api\GiftCardRepositoryInterface $giftCardRepository,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Mageplaza\GiftCard\Model\ResourceModel\GiftCard $giftCardResource,
        \Mageplaza\GiftCard\Model\ResourceModel\GiftCardHistory $giftCardHistoryResource,
        \Mageplaza\GiftCard\Model\GiftCardHistoryFactory $giftCardHistoryFactory,
        \Magento\Customer\Model\Session $currentSession,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
    ) {
        $this->giftCardHistoryResource = $giftCardHistoryResource;
        $this->giftCardHistoryFactory = $giftCardHistoryFactory;
        $this->resourceConnection = $resourceConnection;
        $this->giftCardRepository = $giftCardRepository;
        $this->giftCardFactory = $giftCardFactory;
        $this->currentSession = $currentSession;
        $this->giftCardResource = $giftCardResource;
        $this->_pageFactory = $pageFactory;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
        return parent::__construct($context);
    }
    /**
     * View page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $code = strtoupper($this->getRequest()->getParam('code'));
        $giftCard = $this->giftCardFactory->create();
        $this->giftCardResource->load($giftCard, $code, 'code');
        $giftCardHistory = $this->giftCardHistoryFactory->create();
        $this->giftCardHistoryResource->load($giftCardHistory, $giftCard->getId(), 'giftcard_id');
        $customer =  $this->currentSession->getCustomer();

        if ($giftCard->getId()) {
            try {
                $balanceCustomer = $customer->getGiftcardBalance() + ($giftCard->getBalance() - $giftCard->getAmountUsed());

                if ($giftCardHistory->getAmount() > 0) {
                    $giftCardHistory->setAction('redeem');
                    $giftCardHistory->setAmount(0);
                    $this->saveBalanceCustomer($balanceCustomer, $customer->getId());
                    $this->giftCardHistoryResource->save($giftCardHistory);
                    $giftCard->setBalance(0);
                    $this->giftCardResource->save($giftCard);
                    $this->messageManager->addSuccessMessage(__('You redeemed the gift card ' . $giftCard->getCode() . ' successfully!.'));
                } else {
                    $this->messageManager->addErrorMessage("This gift card code has expired, please use another code!");
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
            } catch (\Throwable $e) {
                $this->messageManager->addExceptionMessage($e, __('Some thing went wrong!'));
            }
        } else {
            $this->messageManager->addErrorMessage('Gift code not exist!');
        }
        $this->cacheFlush();
        $redirect = $this->resultRedirectFactory->create();
        return $redirect->setPath('*/*/');
    }

    private function saveBalanceCustomer($balanceCustomer, $id)
    {
        $tableName = 'customer_entity';
        $connection = $this->resourceConnection->getConnection();
        $sql = "UPDATE " . $tableName . " SET giftcard_balance = " . $balanceCustomer . " WHERE entity_id = " . $id;
        $connection->query($sql);
    }

    public function cacheFlush()
    {
        $types = array('config', 'layout', 'block_html', 'collections', 'reflection', 'db_ddl', 'eav', 'config_integration', 'config_integration_api', 'full_page', 'translate', 'config_webservice');
        foreach ($types as $type) {
            $this->cacheTypeList->cleanType($type);
        }
        foreach ($this->cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
    }
}
