<?php


namespace VinaiKopp\PostCodeFilter;


class CartPageRenderingTest extends ControllerTestCase
{
    /**
     * @var \Mage_Checkout_CartController
     */
    private $controller;

    /**
     * @var \Mage_Checkout_Model_Cart|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockCart;

    /**
     * @var \Mage_Sales_Model_Quote|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockQuote;

    protected function setUp()
    {
        parent::setUp();
        $this->prepareMockCart();
        $this->controller = $this->createControllerInstance(\Mage_Checkout_CartController::class);
    }

    /**
     * @return \Mage_Checkout_CartController
     */
    protected function getControllerUnderTest()
    {
        return $this->controller;
    }

    /**
     * @test
     */
    public function itShouldAddAJavaScriptAssetToThePage()
    {
        $jsFile = 'js/vinaikopp/postcodefilter.js';
        $this->dispatchRoute('checkout', 'cart', 'index');
        $block = \Mage::app()->getLayout()->getBlock('head');
        $assets = $block->getData('items');
        $this->assertArrayHasKey('skin_js/' . $jsFile, $assets);
        $this->assertFileExists(\Mage::getBaseDir('skin') . '/frontend/base/default/' . $jsFile);
    }

    private function prepareMockCart()
    {
        $this->mockQuote = $this->getMock(\Mage_Sales_Model_Quote::class, [], [], '', false);
        $this->mockQuote->expects($this->any())->method('getMessages')->willReturn([]);
        $this->mockCart = $this->getMock(\Mage_Checkout_Model_Cart::class, ['getQupte'], [], '', false);
        $this->mockCart->expects($this->any())->method('getQuote')->willReturn($this->mockQuote);
        \Mage::unregister('_singleton/checkout/cart');
        \Mage::register('_singleton/checkout/cart', $this->mockCart);
    }
}
