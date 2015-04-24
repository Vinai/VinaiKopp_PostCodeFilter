<?php

namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\ReadModel\Rule;
use VinaiKopp\PostCodeFilter\UseCases\AdminAddsRule;
use VinaiKopp\PostCodeFilter\UseCases\AdminDeletesRule;
use VinaiKopp\PostCodeFilter\UseCases\AdminUpdatesRule;
use VinaiKopp\PostCodeFilter\UseCases\AdminViewsSingleRule;

/**
 * @covers \VinaiKopp_PostCodeFilter_Adminhtml_Vinaikopp_PostcodefilterController
 */
class PostcodefilterControllerTest extends ControllerTestCase
{
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

    protected function setUp()
    {
        parent::setUp();
        
        $className = \VinaiKopp_PostCodeFilter_Adminhtml_Vinaikopp_PostcodefilterController::class;
        $this->controller = $this->createControllerInstance($className);
        
        $this->mockAddUseCase = $this->getMock(AdminAddsRule::class, [], [], '', false);
        $this->mockDeleteUseCase = $this->getMock(AdminDeletesRule::class, [], [], '', false);
        $this->mockUpdateUseCase = $this->getMock(AdminUpdatesRule::class, [], [], '', false);
        $this->mockViewSingleRuleUseCase = $this->getMock(AdminViewsSingleRule::class, [], [], '', false);

        $this->controller->setAddUseCase($this->mockAddUseCase);
        $this->controller->setDeleteUseCase($this->mockDeleteUseCase);
        $this->controller->setUpdateRuleUseCase($this->mockUpdateUseCase);
        $this->controller->setViewSingleRuleUseCase($this->mockViewSingleRuleUseCase);

    }

    /**
     * @return \Mage_Core_Controller_Varien_Action
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
        $this->dispatchRoute('adminhtml', 'vinaikopp_postcodefilter', $action);
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
        $this->dispatch('index');
        $this->assertBlockPresent(
            'vinaikopp_postcodefilter_grid_container',
            \Mage_Adminhtml_Block_Widget_Grid_Container::class
        );
    }

    /**
     * @test
     */
    public function itShouldInstantiateAFormContainerBlockForTheNewAction()
    {
        $this->dispatch('new');
        $this->assertBlockPresent(
            'vinaikopp_postcodefilter_form_container',
            \Mage_Adminhtml_Block_Widget_Form_Container::class
        );
    }

    /**
     * @test
     */
    public function itShouldInstantiateTheFormContainerBlockForTheEditAction()
    {
        $stubRule = $this->getMock(Rule::class);
        $stubRule->method('getCustomerGroupIdValues')->willReturn([4]);
        $stubRule->method('getCountryValue')->willReturn('QQ');
        $stubRule->method('getPostCodeValues')->willReturn(['A', 'B']);
        $this->mockViewSingleRuleUseCase->expects($this->once())->method('fetchRule')->willReturn($stubRule);
        
        $this->dispatch('edit');
        
        $this->assertSame($stubRule, \Mage::registry('current_rule'));
        $this->assertBlockPresent(
            'vinaikopp_postcodefilter_form_container',
            \Mage_Adminhtml_Block_Widget_Form_Container::class
        );
    }

    /**
     * @test
     */
    public function itShouldAddThePostedRules()
    {
        $this->getMockRequest()->method('getMethod')->willReturn('POST');
        $this->getMockRequest()->method('getPost')->willReturnMap([
            ['country', null, 'DE'],
            ['customer_group_ids', null, [0, 1]],
            ['post_codes', null, "1234\n5678,\n1313, 4444"]
        ]);
        $this->mockAddUseCase->expects($this->once())->method('addRuleFromScalars');
        $this->dispatch('create');
    }

    /**
     * @test
     */
    public function itShouldUpdateThePostedRules()
    {
        $this->getMockRequest()->method('getMethod')->willReturn('POST');
        $this->getMockRequest()->method('getParam')->willReturnMap([
            ['old_country', null, 'DE'],
            ['old_customer_group_ids', null, '0,1']
        ]);
        $this->getMockRequest()->method('getPost')->willReturnMap([
            ['country', null, 'DE'],
            ['customer_group_ids', null, [0, 1]],
            ['post_codes', null, "1234\n5678,\n1313, 4444"]
        ]);
        
        $this->mockUpdateUseCase->expects($this->once())->method('updateRuleFromScalars');
        $this->dispatch('update');
    }

    /**
     * @test
     */
    public function itShouldDeleteTheSubmittedRules()
    {
        $this->getMockRequest()->method('getParam')->willReturnMap([
            ['country', null, 'DE'],
            ['customer_group_ids', null, '0,1,3']
        ]);
        $this->mockDeleteUseCase->expects($this->once())->method('deleteRuleFromScalars');
        $this->dispatch('delete');
    }
}
