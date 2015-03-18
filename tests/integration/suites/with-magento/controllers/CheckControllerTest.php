<?php

namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\UseCases\CustomerChecksPostCode;

/**
 * @covers \VinaiKopp_PostCodeFilter_Frontend_CheckController 
 */
class CheckControllerTest extends ControllerTestCase
{
    /**
     * @var \VinaiKopp_PostCodeFilter_Frontend_CheckController
     */
    private $controller;

    /**
     * @var CustomerChecksPostCode|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockCheckPostCodeUseCase;

    protected function setUp()
    {
        parent::setUp();
        $this->controller = $this->createControllerInstance(\VinaiKopp_PostCodeFilter_Frontend_CheckController::class);
        $this->mockCheckPostCodeUseCase = $this->getMock(CustomerChecksPostCode::class, [], [], '', false);
        $this->controller->setCheckPostCodeUseCase($this->mockCheckPostCodeUseCase);
    }

    /**
     * @return \VinaiKopp_PostCodeFilter_Frontend_CheckController
     */
    protected function getControllerUnderTest()
    {
        return $this->controller;
    }

    /**
     * @param string $action
     */
    private function dispatch($action)
    {
        $this->dispatchRoute('postcodefilter', 'check', $action);
    }

    /**
     * @test
     */
    public function itShouldBeAFrontendActionController()
    {
        $this->assertInstanceOf(\Mage_Core_Controller_Front_Action::class, $this->controller);
    }

    /**
     * @test
     */
    public function itShouldReturnAJsonContentType()
    {
        $this->getMockRequest()->expects($this->any())->method('getParam')->willReturnMap([
            ['postcode', null, '69123'],
            ['country', null, 'DE'],
        ]);
        $transport = new \stdClass();
        $transport->jsonContentTypeSet = false;
        $this->getMockResponse()->expects($this->atLeastOnce())->method('setHeader')
            ->willReturnCallback(function ($name, $value) use ($transport) {
                $match = ($name === 'content-type' && $value === 'application/json');
                $transport->jsonContentTypeSet = $transport->jsonContentTypeSet || $match;
            });
        $this->dispatch('index');
        $this->assertTrue($transport->jsonContentTypeSet, sprintf('Content type header not set to application/json'));
    }

    /**
     * @test
     * @dataProvider useCaseResponseDataProvider
     * @param bool $mayOrder
     * @param string $postCode
     * @param string $country
     * @param int|string $customerGroupId
     * @param string $message
     */
    public function itShouldSetTheReturnJsonResponse(
        $mayOrder,
        $postCode,
        $country,
        $customerGroupId,
        $message
    ) {
        $this->getMockRequest()->expects($this->any())->method('getParam')->willReturnMap([
            ['postcode', null, $postCode],
            ['country', null, $country],
            ['customer_group_id', null, $customerGroupId]
        ]);

        $this->mockCheckPostCodeUseCase->expects($this->once())->method('mayOrder')
            ->with($customerGroupId, $country, $postCode)
            ->willReturn($mayOrder);
        $expectedResponse = json_encode([
            'country' => $country,
            'postcode' => $postCode,
            'may_order' => $mayOrder,
            'message' => $message
        ]);
        $this->getMockResponse()->expects($this->once())->method('setBody')->with($expectedResponse);
        $this->dispatch('index');
    }

    public function useCaseResponseDataProvider()
    {
        // $mayOrder, $postCode, $country, $customerGroupId, $message
        $errorMessage = 'The shipping address postcode "69123" is not within our delivery area.';
        return [
            'may order' => [true, '69123', 'DE', '2', ''],
            'may not order' => [false, '69123', 'DE', '2', $errorMessage],
            'customer group defaults to 0' => [true, '69123', 'DE', null, ''],
        ];
    }

    /**
     * @test
     */
    public function itShouldHandleExceptionsGracefully()
    {
        $testException = new \Exception('Test Exception');
        $this->mockCheckPostCodeUseCase->expects($this->any())->method('mayOrder')
            ->willThrowException($testException);
        $expectedResponse = json_encode([
            'error' => $testException->getMessage(),
            'may_order' => false,
        ]);
        $this->getMockResponse()->expects($this->once())->method('setBody')->with($expectedResponse);
        $this->dispatch('index');
    }
}
