<?php


namespace VinaiKopp\PostCodeFilter;

/**
 * @covers \VinaiKopp\PostCodeFilter\RuleToDelete
 */
class RuleToDeleteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RuleToDelete
     */
    private $ruleToDelete;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|CustomerGroupId
     */
    private $stubCustomerGroupId;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Country
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
