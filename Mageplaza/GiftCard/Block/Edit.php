<?php
namespace Mageplaza\GiftCard\Block;

class Edit extends \Magento\Framework\View\Element\Template
{
    protected $giftCardFactory;
    protected $giftCardRepository;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mageplaza\GiftCard\Api\GiftCardRepositoryInterface $giftCardRepository,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        array $data = []
    ) {
        $this->giftCardRepository = $giftCardRepository;
        $this->giftCardFactory= $giftCardFactory;
        parent::__construct($context, $data);
    }

    public function getTitle()
    {
        return __('Edit Gift Card');
    }

    public function getGiftCard(){
        $id = $this->getRequest()->getParam('id');
        $data = $this->giftCardRepository->getById($id);
        return $data;     
    }

    public function toSave(){
        return $this->getUrl('plaza/index/test');
    }

}
