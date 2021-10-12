<?php
namespace Mageplaza\GiftCard\Block;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Math\Random;

class Base extends \Magento\Framework\View\Element\Template
{
    protected $giftCardRepository;
    protected $searchCriteriaBuilder;
    protected $mathRandom;
    
    public function __construct(
        \Mageplaza\GiftCard\Api\GiftCardRepositoryInterface $giftCardRepository,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = [],
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Random $mathRandom
    ) {
        $this->mathRandom = $mathRandom;
        $this->giftCardRepository = $giftCardRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context, $data);
    }

    public function getAll(){
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $giftCardSearchResults = $this->giftCardRepository->getList($searchCriteria);
        return $giftCardSearchResults->getItems();
    }
    
    public function toSave(){
        return $this->getUrl('plaza/index/test');
    }

    public function toEdit($id){
        return $this->getUrl('plaza/index/edit?id='.$id);
    }

    public function toDelete($id){
        return $this->getUrl('plaza/index/delete?id='.$id);
    }

    public function getRandomNumber($min, $max){
        $randNumber = $this->mathRandom->getRandomNumber($min, $max );
        return $randNumber;
    }

    public function getRandomChar($length){
        $randChars = $this->mathRandom->getRandomString($length);
        return $randChars;
    }

}
