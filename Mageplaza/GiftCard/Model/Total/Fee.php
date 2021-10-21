<?php

namespace Mageplaza\GiftCard\Model\Total;

/**
 * Class Custom
 * @package Meetanshi\HelloWorld\Model\Total\Quote
 */
class Fee extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;

    protected $sessionFactory;

    protected $giftCardFactory;

    protected $giftCardResource;

    /**
     * Custom constructor.
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Checkout\Model\SessionFactory $sessionFactory,
        \Mageplaza\GiftCard\Model\GiftCardFactory $giftCardFactory,
        \Mageplaza\GiftCard\Model\ResourceModel\GiftCard $giftCardResource
    ) {
        $this->giftCardFactory = $giftCardFactory;
        $this->giftCardResource = $giftCardResource;
        $this->_priceCurrency = $priceCurrency;
        $this->sessionFactory = $sessionFactory;
        $this->setCode('giftcard');
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);
        $giftCardAmoutAvailable = $this->getGiftCardAmount();
        $discount =  $this->_priceCurrency->convert($giftCardAmoutAvailable);
        $total->addTotalAmount($this->getCode(), -$discount);
        $total->addBaseTotalAmount($this->getCode(), -$giftCardAmoutAvailable);
        $total->setBaseGrandTotal($total->getBaseGrandTotal() - $giftCardAmoutAvailable);
        $quote->setCustomDiscount(-$discount);
        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     */
    protected function clearValues(\Magento\Quote\Model\Quote\Address\Total  $total)
    {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     */
    public function fetch(
        \Magento\Quote\Model\Quote  $quote,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        return [
            'code' => $this->getCode(),
            'title' => 'Gift Card',
            'value' => '-'.$this->getGiftCardAmount()
        ];
    }

    public function getGiftCardAmount()
    {
        $code = $this->sessionFactory->create()->getGiftCode();
        $giftCard = $this->giftCardFactory->create();
        $this->giftCardResource->load($giftCard, $code, 'code');
        $valueAvailable = $giftCard->getBalance() - $giftCard->getAmountUsed();
        return $valueAvailable;
    }
}
