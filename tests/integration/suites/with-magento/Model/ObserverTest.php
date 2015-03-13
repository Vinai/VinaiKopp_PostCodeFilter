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
    private function getMockEvent()
    {
        $stubEvent = $this->getMock(Event::class, ['getData', 'getQuote'], [], '', false);
        $stubEvent->expects($this->any())->method('getData')->with('quote')->willReturn($this->getMockQuote());
        $stubEvent->expects($this->any())->method('getQuote')->willReturn($this->getMockQuote());
        return $stubEvent;
    }

    /**
     * @return \Mage_Sales_Model_Quote|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockQuote()
    {
        $mockQuote = $this->getMock(\Mage_Sales_Model_Quote::class, [], [], '', false);
        $mockQuote->expects($this->any())->method('getShippingAddress')->willReturn($this->getMockQuoteAddress());
        $mockQuote->expects($this->any())->method('getCustomerGroupId')->willReturn($this->testCustomerGroupId);
        return $mockQuote;
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
        $mockAddress->expects($this->any())->method('getPostcode')->willReturn($this->testPostcode);
        $mockAddress->expects($this->any())->method('getCountry')->willReturn($this->testCountry);
        return $mockAddress;
    }

    protected function setUp()
    {
        $this->observer = new \VinaiKopp_PostCodeFilter_Model_Observer();
        $this->mockUseCase = $this->getMock(CustomerChecksPostCode::class, [], [], '', false);
        $this->observer->setCustomerChecksPostCodeUseCase($this->mockUseCase);
    }

    /**
     * @test
     */
    public function itShouldCheckTheUseCaseIfTheCustomerMayOrder()
    {
        $this->mockUseCase->expects($this->once())->method('mayOrder')->willReturn(true);
        $this->observer->salesModelServiceQuoteSubmitBefore($this->getMockEvent());
    }

    /**
     * @test
     * @expectedException \Mage_Core_Exception
     * @expectedExceptionMessage The shipping address is not covered by the delivery area.
     */
    public function itShouldThrowAnExceptionIfTheCustomerMayNotOrder()
    {
        $this->mockUseCase->expects($this->once())->method('mayOrder')->willReturn(false);
        $this->observer->salesModelServiceQuoteSubmitBefore($this->getMockEvent());
    }
}
