<?php

/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace AHT\OrderCanceled\Controller\Order;

use Magento\Sales\Controller\OrderInterface;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Controller\AbstractController\OrderLoaderInterface;
use Magento\Framework\Registry;

class Cancel extends \Magento\Framework\App\Action\Action implements OrderInterface
{
    /**
     * @var \Magento\Sales\Api\OrderManagementInterface
     */
    protected $order;

    /**
     * @var \Magento\Sales\Controller\AbstractController\OrderLoaderInterface
     */
    protected $orderLoader;

    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \Magento\Sales\Api\OrderManagementInterface $orderManagementInterface,
        \Magento\Checkout\Model\Session $checkoutSession,
        OrderLoaderInterface $orderLoader,
        Registry $registry,
        Context $context
    ) {
        $this->order = $orderManagementInterface;
        $this->orderLoader = $orderLoader;
        $this->registry = $registry;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->orderLoader->load($this->_request);
        if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
            return $result;
        }
        $order = $this->registry->registry('current_order');
        //$orderId = 1; 
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->order->cancel($order->getId());

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->checkoutSession->getUseNotice(true)) {
                $this->messageManager->addNoticeMessage($e->getMessage());
            } else {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/*/history');
    }
}
