<?php

use \Varien_Event_Observer as Event;
use VinaiKopp\PostCodeFilter\UseCases\CustomerSpecifiesShippingAddress;

class VinaiKopp_PostCodeFilter_Model_Observer
{
    /**
     * @var CustomerSpecifiesShippingAddress
     */
    private $checkPostCodeUseCase;

    public function salesModelServiceQuoteSubmitBefore(Event $event)
    {
        $quote = $event->getData('quote');
        if (!($this->quoteMayBeOrdered($quote, $quote->getShippingAddress()))) {
            $this->abortOrderPlacement($quote->getShippingAddress());
        }
    }

    public function checkoutTypeMultishippingSetShippingItems(Event $event)
    {
        /** @var \Mage_Sales_Model_Quote $quote */
        $quote = $event->getData('quote');
        foreach ($quote->getAllShippingAddresses() as $address) {
            if (!$this->quoteMayBeOrdered($quote, $address)) {
                $this->abortOrderPlacement($address);
            }
        }
    }

    public function controllerActionPostdispatchCheckoutOnepageSaveShipping(Event $event)
    {
        $this->checkPostCodeIsAllowedIfNextStepIsShippingMethod($event);
    }

    public function controllerActionPostdispatchCheckoutOnepageSaveBilling(Event $event)
    {
        $this->checkPostCodeIsAllowedIfNextStepIsShippingMethod($event);
    }

    /**
     * @param Varien_Event_Observer $event
     */
    private function checkPostCodeIsAllowedIfNextStepIsShippingMethod(Event $event)
    {
        /** @var Mage_Core_Controller_Response_Http $response */
        $response = $event->getData('controller_action')->getResponse();
        if ($this->isNextStepShippingMethod($response)) {
            $quote = $this->getCurrentCustomerQuote();
            if (!$this->quoteMayBeOrdered($quote, $quote->getShippingAddress())) {
                $this->setErrorJsonResponse($response, $quote->getShippingAddress());
            }
        }
    }

    /**
     * @param Mage_Sales_Model_Quote $quote
     * @param Mage_Sales_Model_Quote_Address $shippingAddress
     * @return bool
     */
    private function quoteMayBeOrdered(Mage_Sales_Model_Quote $quote, Mage_Sales_Model_Quote_Address $shippingAddress)
    {
        return $this->getCustomerChecksPostCodeUseCase()->isAllowed(
            (int)$quote->getCustomerGroupId(),
            $shippingAddress->getCountry(),
            $shippingAddress->getPostcode()
        );
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @throws Mage_Core_Exception
     */
    private function abortOrderPlacement(Mage_Sales_Model_Quote_Address $address)
    {
        Mage::throwException($this->getTranslatedMayNotOrderErrorMessage($address->getPostcode()));
        // @codeCoverageIgnoreStart
    }

    // @codeCoverageIgnoreEnd

    public function setCustomerChecksPostCodeUseCase(CustomerSpecifiesShippingAddress $customerChecksPostCode)
    {
        $this->checkPostCodeUseCase = $customerChecksPostCode;
    }

    /**
     * @return CustomerSpecifiesShippingAddress
     */
    private function getCustomerChecksPostCodeUseCase()
    {
        if (!$this->checkPostCodeUseCase) {
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

    /**
     * @param Mage_Core_Controller_Response_Http $response
     * @return bool
     */
    private function isNextStepShippingMethod(Mage_Core_Controller_Response_Http $response)
    {
        $result = json_decode($response->getBody(), true);
        return $this->isJsonSuccessResponse($result) && 'shipping_method' === $result['goto_section'];
    }

    /**
     * @param array[]|null $result
     * @return bool
     */
    private function isJsonSuccessResponse($result)
    {
        return is_array($result) && (!array_key_exists('error', $result) || !$result['error']);
    }

    /**
     * @param Mage_Core_Controller_Response_Http $response
     * @param Mage_Sales_Model_Quote_Address $address
     */
    private function setErrorJsonResponse(
        Mage_Core_Controller_Response_Http $response,
        Mage_Sales_Model_Quote_Address $address
    ) {
        $response->setBody(json_encode($this->buildErrorResponseContent($address->getPostcode())));
    }

    /**
     * @param string $postcode
     * @return string[]
     */
    private function buildErrorResponseContent($postcode)
    {
        return [
            'error' => 1,
            'message' => $this->getTranslatedMayNotOrderErrorMessage($postcode)
        ];
    }

    /**
     * @param string $postcode
     * @return string
     */
    private function getTranslatedMayNotOrderErrorMessage($postcode)
    {
        return $this->helper()->__(Mage::getStoreConfig('vinaikopp/postcodefilter/error_message'), $postcode);
    }

    /**
     * @return Mage_Sales_Model_Quote
     */
    private function getCurrentCustomerQuote()
    {
        return Mage::getSingleton('checkout/cart')->getQuote();
    }
}
