<?php
namespace Mageplaza\GiftCard\Block\Adminhtml\GiftCard;

use Magento\Backend\Model\Auth\SessionFactory;
use Magento\Customer\Model\SessionFactory as CustomerSessionFactory;

class Account extends \Magento\Framework\View\Element\Template
{
    protected $authSessionFactory;
    protected $customerSessionFactory;

    public function __construct(SessionFactory $authSessionFactory, CustomerSessionFactory $customerSessionFactory)
    {
        $this->authSessionFactory = $authSessionFactory;
        $this->customerSessionFactory = $customerSessionFactory;
    }
    public function getCurrentUser()
    {
        $authSession = $this->authSessionFactory->create();
        $customerSession = $this->customerSessionFactory->create();
        $currentUser = $authSession ? $authSession->getUser() : $customerSession->getCustomer();
        return $currentUser;
    }

    public function getUserName(){
        return $this->getCurrentUser()->getUserName();
    }
}

