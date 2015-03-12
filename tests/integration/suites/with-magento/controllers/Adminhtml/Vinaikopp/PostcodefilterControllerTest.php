<?php

namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\Query\RuleResult;
use VinaiKopp\PostCodeFilter\UseCases\AdminAddsRule;
use VinaiKopp\PostCodeFilter\UseCases\AdminDeletesRule;
use VinaiKopp\PostCodeFilter\UseCases\AdminUpdatesRule;
use VinaiKopp\PostCodeFilter\UseCases\AdminViewsSingleRule;

/**
 * @covers \VinaiKopp_PostCodeFilter_Adminhtml_Vinaikopp_PostcodefilterController
 */
class PostcodefilterControllerTest extends IntegrationTestCase
{
    private $className = \VinaiKopp_PostCodeFilter_Adminhtml_Vinaikopp_PostcodefilterController::class;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Mage_Core_Controller_Request_Http
     */
    private $mockRequest;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Mage_Core_Controller_Response_Http
     */
    private $mockResponse;

    /**
     * @var \VinaiKopp_PostCodeFilter_Adminhtml_Vinaikopp_PostcodefilterController
     */
    private $controller;

    /**
     * @var AdminAddsRule|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockAddUseCase;

    /**
     * @var AdminDeletesRule|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockDeleteUseCase;

    /**
     * @var AdminViewsSingleRule|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockViewSingleRuleUseCase;

    /**
     * @var AdminUpdatesRule|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockUpdateUseCase;

    /**
     * @return string
     */
    private function getClassFile()
    {
        $controllersDir = \Mage::getModuleDir('controllers', 'VinaiKopp_PostCodeFilter');
        return $controllersDir . '/Adminhtml/Vinaikopp/PostcodefilterController.php';
    }

    /**
     * @return \VinaiKopp_PostCodeFilter_Adminhtml_Vinaikopp_PostcodefilterController
     */
    private function createInstance()
    {
        if (! class_exists($this->className, false)) {
            require $this->getClassFile();
        }
        
        return new \VinaiKopp_PostCodeFilter_Adminhtml_Vinaikopp_PostcodefilterController(
            $this->mockRequest,
            $this->mockResponse
        );
    }

    /**
     * @param string $action
     */
    private function dispatchAction($action)
    {
        $this->mockRequest->expects($this->any())->method('getRequestedRouteName')
            ->willReturn('adminhtml');
        $this->mockRequest->expects($this->any())->method('getRequestedControllerName')
            ->willReturn('vinaikopp_postcodefilter');
        $this->mockRequest->expects($this->any())->method('getRequestedActionName')
            ->willReturn($action);
        $this->mockRequest->setDispatched(true);
        \Mage::getSingleton('admin/session')->setData('user', $this->getMock(\Mage_Admin_Model_User::class));
        $this->controller->dispatch($action);

    }

    protected function setUp()
    {
        self::resetMagento();
        
        $this->mockRequest = $this->getMock(
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
        $this->mockResponse = $this->getMock(\Mage_Core_Controller_Response_Http::class);
        $this->mockAddUseCase = $this->getMock(AdminAddsRule::class, [], [], '', false);
        $this->mockDeleteUseCase = $this->getMock(AdminDeletesRule::class, [], [], '', false);
        $this->mockUpdateUseCase = $this->getMock(AdminUpdatesRule::class, [], [], '', false);
        $this->mockViewSingleRuleUseCase = $this->getMock(AdminViewsSingleRule::class, [], [], '', false);
        
        $this->controller = $this->createInstance();
        $this->controller->setAddUseCase($this->mockAddUseCase);
        $this->controller->setDeleteUseCase($this->mockDeleteUseCase);
        $this->controller->setUpdateRuleUseCase($this->mockUpdateUseCase);
        $this->controller->setViewSingleRuleUseCase($this->mockViewSingleRuleUseCase);

    }

    /**
     * @test
     */
    public function itShouldBeAnAdminActionController()
    {
        $this->assertInstanceOf(\Mage_Adminhtml_Controller_Action::class, $this->controller);
    }

    /**
     * @test
     */
    public function itShouldInstantiateAGridContainerBlock()
    {
        $this->dispatchAction('index');
        $block = \Mage::app()->getLayout()->getBlock('vinaikopp_postcodefilter_grid_container');
        $this->assertInstanceOf(\Mage_Adminhtml_Block_Widget_Grid_Container::class, $block);
    }

    /**
     * @test
     */
    public function itShouldInstantiateAFormContainerBlockForTheNewAction()
    {
        $this->dispatchAction('new');
        $this->assertBlockPresent('vinaikopp_postcodefilter_form_container');
    }

    /**
     * @test
     */
    public function itShouldInstantiateTheFormContainerBlockForTheEditAction()
    {
        $stubRule = $this->getMock(RuleResult::class);
        $this->mockViewSingleRuleUseCase->expects($this->once())->method('fetchRule')->willReturn($stubRule);
        
        $this->dispatchAction('edit');
        
        $this->assertSame($stubRule, \Mage::registry('current_rule'));
        $this->assertBlockPresent('vinaikopp_postcodefilter_form_container');
    }

    /**
     * @test
     */
    public function itShouldAddThePostedRules()
    {
        $this->mockRequest->expects($this->any())->method('getMethod')->willReturn('POST');
        $this->mockRequest->expects($this->any())->method('getPost')->willReturnMap([
            ['country', null, 'DE'],
            ['customer_group_ids', null, [0, 1]],
            ['post_codes', null, "1234\n5678,\n1313, 4444"]
        ]);
        $this->mockAddUseCase->expects($this->once())->method('addRule');
        $this->dispatchAction('create');
    }

    /**
     * @test
     */
    public function itShouldUpdateThePostedRules()
    {
        $this->mockRequest->expects($this->any())->method('getMethod')->willReturn('POST');
        $this->mockRequest->expects($this->any())->method('getParam')->willReturnMap([
            ['old_country', null, 'DE'],
            ['old_customer_group_ids', null, '0,1']
        ]);
        $this->mockRequest->expects($this->any())->method('getPost')->willReturnMap([
            ['country', null, 'DE'],
            ['customer_group_ids', null, [0, 1]],
            ['post_codes', null, "1234\n5678,\n1313, 4444"]
        ]);
        
        $this->mockUpdateUseCase->expects($this->once())->method('updateRule');
        $this->dispatchAction('update');
    }

    /**
     * @test
     */
    public function itShouldDeleteTheSubmittedRules()
    {
        $this->mockRequest->expects($this->any())->method('getParam')->willReturnMap([
            ['old_country', null, 'DE'],
            ['old_customer_group_ids', null, '0,1,3']
        ]);
        $this->mockDeleteUseCase->expects($this->once())->method('deleteRule');
        $this->dispatchAction('delete');
    }

    /**
     * @param string $blockName
     */
    private function assertBlockPresent($blockName)
    {
        $block = \Mage::app()->getLayout()->getBlock($blockName);
        $this->assertInstanceOf(\Mage_Adminhtml_Block_Widget_Form_Container::class, $block);
    }
}
