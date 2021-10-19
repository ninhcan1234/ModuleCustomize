<?php

namespace Mageplaza\GiftCard\Plugin;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Quote\Api\Data\CartExtensionFactory;

class CustomerRepositoryInterface
{
    protected $collectionFactory;
    protected $customerRepository;

    public function __construct(
        CollectionFactory $collectionFactory,
        CustomerRepository $customerRepository
    )
    {
        $this->collectionFactory = $collectionFactory;
        $this->customerRepository = $customerRepository;
    }

    public function afterGet(CustomerRepository $subject, CustomerInterface $customer)
    {
        if ($customer->getExtensionAttributes() && $customer->getExtensionAttributes()->getGiftcardBalance()) {
            return $customer;
        }

        $giftCardBalance = $this->getGiftcardBalance($customer->getId());
        $extensionAttribute = $customer->getExtensionAttributes()->setGiftcardBalance($giftCardBalance);
        $customer->setExtensionAttributes($extensionAttribute);
        return $customer;
    }

    public function getGiftcardBalance($customerId)
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('entity_id', ['eq' => $customerId])
            ->getFirstItem()->getData('giftcard_balance');
        return $collection;
    }

    public function afterGetList(
        \Magento\Customer\Api\CustomerRepositoryInterface $subject,
        \Magento\Customer\Api\Data\CustomerSearchResultsInterface $searchResults
    ) : \Magento\Customer\Api\Data\CustomerSearchResultsInterface
    {
        $customers = [];
        foreach ($searchResults->getItems() as $entity) {
            $giftCardBalance = $this->customerRepository->get($entity->getEmail());
    
            $extensionAttributes = $entity->getExtensionAttributes();
            $extensionAttributes->setGiftcardBalance($giftCardBalance);
            $entity->setExtensionAttributes($extensionAttributes);
    
            $customers[] = $entity;
        }
        $searchResults->setItems($customers);
        return $searchResults;
    }
    

}
