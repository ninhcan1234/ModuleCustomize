<?php

namespace AHT\CheckOut\Controller\Quote;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;

class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageFactory;
    protected $jsonFactory;
    protected $json;
    protected $maskedQuoteIdToQuoteId;
    protected $deliveryFactory;
    protected $deliveryRepository;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Framework\Serialize\Serializer\Json $json,
        MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId,
        \AHT\CheckOut\Model\DeliveryFactory $deliveryFactory,
        \AHT\CheckOut\Api\DeliveryRepositoryInterface $deliveryRepository
    ) {
        $this->maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
        $this->json = $json;
        $this->jsonFactory = $jsonFactory;
        $this->pageFactory = $pageFactory;
        $this->deliveryFactory = $deliveryFactory;
        $this->deliveryRepository = $deliveryRepository;
        return parent::__construct($context);
    }

    /**
     * get QuoteId by masked id.
     *
     * @return int
     * @throws LocalizedException
     */
    public function getQuoteIdFromMaskedHash($maskedHashId)
    {
        try {
            $cartId = $this->maskedQuoteIdToQuoteId->execute($maskedHashId);
        } catch (NoSuchEntityException $exception) {
            throw new LocalizedException(
                __('Could not find a cart with ID "%masked_cart_id"', ['masked_cart_id' => $maskedHashId])
            );
        }

        return $cartId;
    }

    public function saveDelivery($quoteId, $date, $comment)
    {
        $delivery = $this->deliveryFactory->create();
        $delivery->setQuoteId($quoteId);
        $delivery->setDeliveryDate($date);
        $delivery->setDeliveryComment($comment);
        $this->deliveryRepository->save($delivery);
    }

    /**
     * View page action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getContent();
        $data = $this->json->unserialize($data);
        $quoteId = $this->getQuoteIdFromMaskedHash($data['quoteId']);
        $this->saveDelivery($quoteId, $data['date'], $data['comment']);
    }
}
