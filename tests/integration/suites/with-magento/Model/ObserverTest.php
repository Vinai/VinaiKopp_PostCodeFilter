<?php

namespace VinaiKopp\PostCodeFilter;

use \Varien_Event_Observer as Event;
use VinaiKopp\PostCodeFilter\UseCases\CustomerChecksPostCode;

/**
 * @covers \VinaiKopp_PostCodeFilter_Model_Observer
 */
class ObserverTest extends IntegrationTestCase
{
    /**
     * @var \VinaiKopp_PostCodeFilter_Model_Observer
     */
    private $observer;

    private $testCountry = 'DE';

    private $testPostcode = '1234';

    private $testCustomerGroupId = 7;

    /**
     * @var CustomerChecksPostCode|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockUseCase;

    /**
     * @return Event|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockEventWithQuote()
    {
        $stubEvent = $this->getMock(Event::class, ['getData', 'getQuote'], [], '', false);
        $mockQuote = $this->getMockQuote();
        $stubEvent->method('getData')->with('quote')->willReturn($mockQuote);
        $stubEvent->method('getQuote')->willReturn($mockQuote);
        return $stubEvent;
    }

    /**
     * @return Event|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockControllerActionPostdispatchCheckoutOnepageSaveShippingEvent()
    {
        $stubEvent = $this->getMock(Event::class, ['getData', 'getControllerAction'], [], '', false);
        $stubController = $this->getMockOnepageController();
        $stubEvent->method('getData')->with('controller_action')
            ->willReturn($stubController);
        $stubEvent->method('getControllerAction')
            ->willReturn($stubController);
        return $stubEvent;
    }

    /**
     * @return \Mage_Sales_Model_Quote|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockQuote()
    {
        $mockQuote = $this->getMock(\Mage_Sales_Model_Quote::class, [], [], '', false);
        $mockQuote->method('getShippingAddress')->willReturn($this->getMockQuoteAddress());
        $mockQuote->method('getCustomerGroupId')->willReturn($this->testCustomerGroupId);
        return $mockQuote;
    }

    /**
     * @return \Mage_Checkout_OnepageController|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockOnepageController()
    {
        $mockController = $this->getMockBuilder(\Mage_Checkout_OnepageController::class)
            ->disableOriginalConstructor()
            ->setMethods(['getResponse', 'getRequest'])
            ->getMock();
        $mockController->method('getResponse')->willReturn(
            $this->getMock(\Mage_Core_Controller_Response_Http::class)
        );
        $mockController->method('getRequest')->willReturn(
            $this->getMock(\Mage_Core_Controller_Request_Http::class, [], [], '', false)
        );
        return $mockController;
    }

    /**
     * @return \Mage_Sales_Model_Quote_Address|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockQuoteAddress()
    {
        $mockAddress = $this->getMockBuilder(\Mage_Sales_Model_Quote_Address::class)
            ->setMethods(['getPostcode', 'getCountry'])
            ->disableOriginalConstructor()
            ->getMock();
        $mockAddress->method('getPostcode')->willReturn($this->testPostcode);
        $mockAddress->method('getCountry')->willReturn($this->testCountry);
        return $mockAddress;
    }

    private function registerMockCart()
    {
        $mockCart = $this->getMock(\Mage_Checkout_Model_Cart::class, [], [], '', false);
        $mockCart->method('getQuote')->willReturn($this->getMockQuote());
        \Mage::register('_singleton/checkout/cart', $mockCart);
        return $mockCart;
    }

    protected function setUp()
    {
        $this->observer = new \VinaiKopp_PostCodeFilter_Model_Observer();
        $this->mockUseCase = $this->getMock(CustomerChecksPostCode::class, [], [], '', false);
        $this->observer->setCustomerChecksPostCodeUseCase($this->mockUseCase);
    }

    protected function tearDown()
    {
        \Mage::unregister('_singleton/checkout/cart');
    }

    /**
     * @test
     */
    public function itShouldCheckTheUseCaseIfTheCustomerMayOrder()
    {
        $this->mockUseCase->expects($this->once())->method('mayOrder')->willReturn(true);
        $this->observer->salesModelServiceQuoteSubmitBefore($this->getMockEventWithQuote());
    }

    /**
     * @test
     * @expectedException \Mage_Core_Exception
     * @expectedExceptionMessage The shipping address postcode
     */
    public function itShouldThrowAnExceptionIfTheCustomerMayNotOrder()
    {
        $this->mockUseCase->expects($this->once())->method('mayOrder')->willReturn(false);
        $this->observer->salesModelServiceQuoteSubmitBefore($this->getMockEventWithQuote());
    }

    public function observerMethodNameProvider()
    {
        return [
            'save_billing' => ['controllerActionPostdispatchCheckoutOnepageSaveBilling'],
            'save_shipping' => ['controllerActionPostdispatchCheckoutOnepageSaveShipping'],
        ];
    }

    /**
     * @test
     * @dataProvider observerMethodNameProvider
     * @param string $methodToTest
     */
    public function itShouldSetAResponseErrorIfTheShippingAddressHasNoValidDestinationPostcode($methodToTest)
    {
        $this->registerMockCart();
        $mockEvent = $this->getMockControllerActionPostdispatchCheckoutOnepageSaveShippingEvent();
        /** @var \PHPUnit_Framework_MockObject_MockObject $mockResponse */
        $mockResponse = $mockEvent->getData('controller_action')->getResponse();
        $mockResponse->expects($this->once())->method('getBody')->willReturn(
            json_encode(['goto_section' => 'shipping_method'])
        );
        $this->mockUseCase->expects($this->once())->method('mayOrder')->willReturn(false);
        $transport = '';
        $mockResponse->expects($this->once())->method('setBody')->willReturnCallback(
            function ($responseContent) use (&$transport, $mockResponse) {
                $transport = $responseContent;
                return $mockResponse;
            }
        );

        $this->observer->$methodToTest($mockEvent);

        $this->assertJson($transport);
        $result = json_decode($transport, true);
        $this->assertInternalType('array', $result, 'Unable to json decode response body');
        $this->assertArrayHasKey('error', $result, 'Response JSON contains no key "error"');
        $this->assertArrayHasKey('message', $result, 'Response JSON contains no key "message"');
        $this->assertEquals(1, $result['error'], 'Response JSON key "error" is not equal to 1');
    }

    /**
     * @test
     * @dataProvider observerMethodNameProvider
     * @param string $methodToTest
     */
    public function itShouldNotChangeTheResponseIfTheShippingAddressMayIsAllowed($methodToTest)
    {
        $this->registerMockCart();
        $mockEvent = $this->getMockControllerActionPostdispatchCheckoutOnepageSaveShippingEvent();
        /** @var \PHPUnit_Framework_MockObject_MockObject $mockResponse */
        $mockResponse = $mockEvent->getData('controller_action')->getResponse();
        $mockResponse->expects($this->once())->method('getBody')->willReturn(
            json_encode(['goto_section' => 'shipping_method'])
        );
        $this->mockUseCase->expects($this->once())->method('mayOrder')->willReturn(true);

        $mockResponse->expects($this->never())->method('setBody');

        $this->observer->$methodToTest($mockEvent);
    }

    /**
     * @test
     */
    public function itShouldCheckThePostcodeOfAllQuoteAddresses()
    {
        $event = $this->getMockEventWithQuote();
        /** @var \Mage_Sales_Model_Quote|\PHPUnit_Framework_MockObject_MockObject $quote */
        $quote = $event->getQuote();
        /** @var \Mage_Sales_Model_Quote_Address|\PHPUnit_Framework_MockObject_MockObject $mockShippingAddress */
        $mockShippingAddress = $quote->getShippingAddress();
        $quote->method('getAllShippingAddresses')->willReturn(
            [$mockShippingAddress, $mockShippingAddress]
        );
        $this->mockUseCase->expects($this->exactly(2))->method('mayOrder')->willReturn(true);

        $this->observer->checkoutTypeMultishippingSetShippingItems($event);
    }

    /**
     * @test
     * @expectedException \Mage_Core_Exception
     */
    public function itShouldThrowAnExceptionIfTheShippingPostcodeIsNotInTheDeliveryArea()
    {
        $event = $this->getMockEventWithQuote();
        /** @var \Mage_Sales_Model_Quote|\PHPUnit_Framework_MockObject_MockObject $quote */
        $quote = $event->getQuote();
        /** @var \Mage_Sales_Model_Quote_Address|\PHPUnit_Framework_MockObject_MockObject $mockShippingAddress */
        $mockShippingAddress = $quote->getShippingAddress();
        $quote->method('getAllShippingAddresses')->willReturn(
            [$mockShippingAddress, $mockShippingAddress]
        );
        $this->mockUseCase->method('mayOrder')->willReturnOnConsecutiveCalls(true, false);

        $this->observer->checkoutTypeMultishippingSetShippingItems($event);
    }
}
