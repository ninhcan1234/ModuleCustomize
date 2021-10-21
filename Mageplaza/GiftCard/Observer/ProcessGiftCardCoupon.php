<?php

namespace Mageplaza\GiftCard\Observer;

class ProcessGiftCardCoupon implements \Magento\Framework\Event\ObserverInterface
{
    protected $giftCardResource;
    protected $giftCardFactory;
    protected $quoteRepository;
    protected $resultRedirectFactory;
    protected $url;
    protected $checkoutSession;
    protected $_actionFlag;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    public function __construct(
        \Mageplaza\GiftCard\Model\ResourceModel\GiftCard $giftCardResource,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\ActionFlag $actionFlag
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->giftCardFactory = $giftCardFactory;
        $this->giftCardResource = $giftCardResource;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->_objectManager = $objectManager;
        $this->url = $url;
        $this->checkoutSession = $checkoutSession;
        $this->_actionFlag = $actionFlag;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\App\Action\Action $controller */
        $controller = $observer->getControllerAction();
        $couponCode = trim($controller->getRequest()->getParam('coupon_code'));
        $remove = $controller->getRequest()->getParam('remove');
        $escaper = $this->_objectManager->get(\Magento\Framework\Escaper::class);

        $giftCard = $this->giftCardFactory->create();
        $this->giftCardResource->load($giftCard, $couponCode, 'code');

        if ($giftCard->getId()) {
            $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
            try {
                $codeLength = strlen($giftCard->getCode());
                $isCodeLengthValid = $codeLength && $codeLength <= \Magento\Checkout\Helper\Cart::COUPON_CODE_MAX_LENGTH;

                if ($codeLength) {

                    if ($isCodeLengthValid && $giftCard->getId() && $giftCard->getBalance() > 0) {
                        $this->checkoutSession->setGiftCode($giftCard->getCode());
                        $this->checkoutSession->setGiftCodeId($giftCard->getId());

                        $this->messageManager->addSuccessMessage(
                            __(
                                'You applied gift code "%1" successfully !',
                                $escaper->escapeHtml($couponCode)
                            )
                        );
                    } else {
                        $this->messageManager->addErrorMessage(
                            __(
                                'The gift code "%1" is not valid or has exprired.',
                                $escaper->escapeHtml($couponCode)
                            )
                        );
                    }
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('We cannot apply the coupon code.'));
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            }

            $observer->getControllerAction()->getResponse()->setRedirect($this->url->getUrl('*/*/'));
            return $this;
        }

        if ($remove == 1 && $this->checkoutSession->getGiftCode($giftCard->getCode())) {
            $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
            $this->checkoutSession->unsGiftCode($giftCard->getCode());
            $this->messageManager->addSuccessMessage(
                __('You cancelled the gift code successfully !')
            );
            $observer->getControllerAction()->getResponse()->setRedirect($this->url->getUrl('*/*/'));
            return $this;
        }
    }
}
