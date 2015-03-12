<?php


namespace VinaiKopp\PostCodeFilter\Command;

use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId;

/**
 * @covers \VinaiKopp\PostCodeFilter\Command\RuleToDelete
 */
class RuleToDeleteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RuleToDelete
     */
    private $ruleToDelete;

    /**
     * @var CustomerGroupId|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubCustomerGroupId;

    /**
     * @var Country|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubCountry;

    protected function setUp()
    {
        $this->stubCustomerGroupId = $this->getMock(CustomerGroupId::class, [], [], '', false);
        $this->stubCountry = $this->getMock(Country::class, [], [], '', false);
        $this->ruleToDelete = new RuleToDelete($this->stubCustomerGroupId, $this->stubCountry);
    }

    /**
     * @test
     */
    public function itShouldReturnTheCustomerGroupId()
    {
        $this->assertSame($this->stubCustomerGroupId, $this->ruleToDelete->getCustomerGroupId());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCustomerGroupIdValue()
    {
        $this->stubCustomerGroupId->expects($this->once())->method('getValue')->willReturn(2);
        $this->assertSame(2, $this->ruleToDelete->getCustomerGroupIdValue());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCountry()
    {
        $this->assertSame($this->stubCountry, $this->ruleToDelete->getCountry());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCountryValue()
    {
        $this->stubCountry->expects($this->once())->method('getValue')->willReturn('XX');
        $this->assertSame('XX', $this->ruleToDelete->getCountryValue());
    }
}
