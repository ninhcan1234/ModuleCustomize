<?php

namespace Mageplaza\GiftCard\Observer;

class ProcessGiftCardCoupon implements \Magento\Framework\Event\ObserverInterface
{
    protected $giftCardResource;
    protected $giftCardFactory;
    protected $cart;
    protected $quoteRepository;
    protected $resultRedirectFactory;
    protected $redirect;
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
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Response\Http $redirect,
        \Magento\Framework\App\ActionFlag $actionFlag
    ) {
        $this->cart = $cart;
        $this->quoteRepository = $quoteRepository;
        $this->giftCardFactory = $giftCardFactory;
        $this->giftCardResource = $giftCardResource;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->_objectManager = $objectManager;
        $this->url = $url;
        $this->redirect = $redirect;
        $this->checkoutSession = $checkoutSession;
        $this->_actionFlag = $actionFlag;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\App\Action\Action $controller */

        $controller = $observer->getControllerAction();
        $couponCode = $controller->getRequest()->getParam('remove') == 1
            ? ''
            : trim($controller->getRequest()->getParam('coupon_code'));
        $codeLength = strlen($couponCode);
        $giftCard = $this->giftCardFactory->create();
        $this->giftCardResource->load($giftCard, $couponCode, 'code');

        if ($giftCard->getId()) {
            $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
            try {
                $isCodeLengthValid = $codeLength && $codeLength <= \Magento\Checkout\Helper\Cart::COUPON_CODE_MAX_LENGTH;

                if ($codeLength) {
                    $escaper = $this->_objectManager->get(\Magento\Framework\Escaper::class);

                    if ($isCodeLengthValid && $giftCard->getId()) {
                        $this->checkoutSession->getQuote()->setCouponCode($couponCode)->save();

                        $this->messageManager->addSuccessMessage(
                            __(
                                'You applied gift code "%1" successfully !',
                                $escaper->escapeHtml($couponCode)
                            )
                        );
                    } else {
                        $this->messageManager->addErrorMessage(
                            __(
                                'The gift code "%1" is not valid.',
                                $escaper->escapeHtml($couponCode)
                            )
                        );
                    }
                } else {
                    $this->messageManager->addSuccessMessage(__('You canceled the gift code.'));
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
    }
}
