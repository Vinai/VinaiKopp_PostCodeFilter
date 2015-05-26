<?php

namespace VinaiKopp\PostCodeFilter;

abstract class ControllerTestCase extends Mage1IntegrationTestCase
{
    /**
     * @var  \Mage_Core_Controller_Request_Http|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRequest;

    /**
     * @var  \Mage_Core_Controller_Response_Http|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockResponse;

    protected function setUp()
    {
        parent::setUp();
        self::resetMagento();
    }

    /**
     * @return \Mage_Core_Controller_Request_Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function createMockRequest()
    {
        return $this->getMock(
            \Mage_Core_Controller_Request_Http::class,
            [
                'getRequestedRouteName',
                'getRequestedControllerName',
                'getRequestedActionName',
                'getMethod',
                'getPost',
                'getParam',
            ]
        );
    }

    /**
     * @return \Mage_Core_Controller_Response_Http|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createMockResponse()
    {
        return $this->getMock(\Mage_Core_Controller_Response_Http::class);
    }

    /**
     * @return \Mage_Core_Controller_Request_Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockRequest()
    {
        if (!$this->mockRequest) {
            $this->mockRequest = $this->createMockRequest();
        }
        return $this->mockRequest;
    }

    /**
     * @return \Mage_Core_Controller_Response_Http|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function getMockResponse()
    {
        if (!$this->mockResponse) {
            $this->mockResponse = $this->createMockResponse();
        }
        return $this->mockResponse;
    }

    /**
     * @param string $className
     * @return string
     */
    protected function getControllerFile($className)
    {
        $parts = explode('_', ltrim($className, '\\'));
        $moduleName = sprintf('%s_%s', $parts[0], $parts[1]);
        $controllersDir = \Mage::getModuleDir('controllers', $moduleName);
        return sprintf('%s/%s.php', $controllersDir, implode('/', array_slice($parts, 2)));
    }

    /**
     * @param string $className
     * @return object
     */
    protected function createControllerInstance($className)
    {
        if (!class_exists($className, false)) {
            require $this->getControllerFile($className);
        }

        return new $className($this->getMockRequest(), $this->getMockResponse());
    }

    /**
     * @param string $frontName
     * @param string $controller
     * @param string $action
     */
    protected function dispatchRoute($frontName, $controller, $action)
    {
        $this->getMockRequest()->method('getRequestedRouteName')
            ->willReturn($frontName);
        $this->getMockRequest()->method('getRequestedControllerName')
            ->willReturn($controller);
        $this->getMockRequest()->method('getRequestedActionName')
            ->willReturn($action);
        $this->getMockRequest()->setDispatched(true);
        \Mage::getSingleton('admin/session')->setData('user', $this->getMock(\Mage_Admin_Model_User::class));
        $this->getControllerUnderTest()->dispatch($action);
    }

    /**
     * @param string $blockName
     * @param string $className
     */
    protected function assertBlockPresent($blockName, $className = \Mage_Core_Block_Abstract::class)
    {
        $block = \Mage::app()->getLayout()->getBlock($blockName);
        $this->assertNotNull($block, sprintf('Block instance with name in layout "%s" not found', $blockName));
        $this->assertInstanceOf($className, $block, sprintf('Block is not an instance of "%s"', $className));
    }

    /**
     * @return \Mage_Core_Controller_Varien_Action
     */
    abstract protected function getControllerUnderTest();
}
