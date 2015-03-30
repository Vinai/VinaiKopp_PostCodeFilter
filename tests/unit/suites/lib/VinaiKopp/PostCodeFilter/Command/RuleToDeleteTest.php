<?php


namespace VinaiKopp\PostCodeFilter\Command;

use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;

/**
 * @covers \VinaiKopp\PostCodeFilter\Command\RuleToDelete
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\Country
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList
 */
class RuleToDeleteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RuleToDelete
     */
    private $ruleToDelete;

    /**
     * @var CustomerGroupIdList|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubCustomerGroupIds;

    /**
     * @var Country|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubCountry;

    protected function setUp()
    {
        $this->stubCustomerGroupIds = $this->getMock(CustomerGroupIdList::class, [], [], '', false);
        $this->stubCountry = $this->getMock(Country::class, [], [], '', false);
        $this->ruleToDelete = new RuleToDelete($this->stubCustomerGroupIds, $this->stubCountry);
    }

    /**
     * @test
     */
    public function itShouldReturnTheCustomerGroupIdList()
    {
        $this->assertSame($this->stubCustomerGroupIds, $this->ruleToDelete->getCustomerGroupIds());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCustomerGroupIdValues()
    {
        $testGroupIds = [2, 3];
        $this->stubCustomerGroupIds->expects($this->once())->method('getValues')->willReturn($testGroupIds);
        $this->assertSame($testGroupIds, $this->ruleToDelete->getCustomerGroupIdValues());
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

    /**
     * @test
     */
    public function itShouldCreateAnInstanceFromScalarValues()
    {
        $ruleToDelete = RuleToDelete::createFromScalars([0, 2, 3], 'GB');
        $this->assertInstanceOf(RuleToDelete::class, $ruleToDelete);
    }
}
