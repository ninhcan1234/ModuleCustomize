<?php
namespace AHT\UiComponent\Block;

class CountryCode extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    protected $countryFactory;
    protected $helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \AHT\UiComponent\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->countryFactory = $countryFactory;
        $this->helper = $helper;
    }

    public function getCountryName($code){
        $country = $this->countryFactory->create()->loadByCode($code);
        return $country->getName();
    }

    public function getPhoneCode(){
        return $this->helper->getPhoneCode();
    }
}
