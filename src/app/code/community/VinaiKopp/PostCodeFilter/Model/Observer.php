<?php

use \Varien_Event_Observer as Event;
use VinaiKopp\PostCodeFilter\UseCases\CustomerChecksPostCode;

class VinaiKopp_PostCodeFilter_Model_Observer
{
    /**
     * @var CustomerChecksPostCode
     */
    private $checkPostCodeUseCase;
    
    public function salesModelServiceQuoteSubmitBefore(Event $event)
    {
        $result = $this->customerMayOrder($event->getData('quote'));
        if (! $result) {
            $this->abortOrderPlacement();
        }
    }

    /**
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    private function customerMayOrder(Mage_Sales_Model_Quote $quote)
    {
        $shippingAddress = $quote->getShippingAddress();
        $result = $this->getCustomerChecksPostCodeUseCase()->mayOrder(
            (int) $quote->getCustomerGroupId(),
            $shippingAddress->getCountry(),
            $shippingAddress->getPostcode()
        );
        return $result;
    }

    /**
     * @throws Mage_Core_Exception
     */
    private function abortOrderPlacement()
    {
        Mage::throwException(
            $this->helper()->__('The shipping address is not covered by the delivery area.')
        );
    }

    public function setCustomerChecksPostCodeUseCase(CustomerChecksPostCode $customerChecksPostCode)
    {
        $this->checkPostCodeUseCase = $customerChecksPostCode;
    }

    private function getCustomerChecksPostCodeUseCase()
    {
        if (! $this->checkPostCodeUseCase) {
            // @codeCoverageIgnoreStart
            $this->checkPostCodeUseCase = $this->helper()->createCustomerChecksPostCodeUseCase();
        }
        // @codeCoverageIgnoreEnd
        return $this->checkPostCodeUseCase;
    }

    /**
     * @return VinaiKopp_PostCodeFilter_Helper_Data
     */
    private function helper()
    {
        return Mage::helper('vinaikopp_postcodefilter');
    }
}
