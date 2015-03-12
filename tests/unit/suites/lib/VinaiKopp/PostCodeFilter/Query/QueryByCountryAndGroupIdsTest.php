<?php


namespace VinaiKopp\PostCodeFilter\Query;

use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;

/**
 * @covers \VinaiKopp\PostCodeFilter\Query\QueryByCountryAndGroupIds
 */
class QueryByCountryAndGroupIdsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QueryByCountryAndGroupIds
     */
    private $ruleQuery;

    /**
     * @var Country|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubCountry;

    /**
     * @var CustomerGroupId|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubCustomerGroupIdList;

    public function setUp()
    {
        $this->stubCountry = $this->getMock(Country::class, [], [], '', false);
        $this->stubCustomerGroupIdList = $this->getMock(CustomerGroupIdList::class, [], [], '', false);
        $this->ruleQuery = new QueryByCountryAndGroupIds($this->stubCountry, $this->stubCustomerGroupIdList);
    }

    /**
     * @test
     */
    public function itShouldReturnTheCountryInstance()
    {
        $this->assertSame($this->stubCountry, $this->ruleQuery->getCountry());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCountryValue()
    {
        $countryValue = 'DE';
        $this->stubCountry->expects($this->once())->method('getValue')->willReturn($countryValue);
        $this->assertSame($countryValue, $this->ruleQuery->getCountryValue());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCustomerGroupIdInstances()
    {
        $this->assertSame($this->stubCustomerGroupIdList, $this->ruleQuery->getCustomerGroupIds());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCustomerGroupIdValues()
    {
        $this->stubCustomerGroupIdList->expects($this->once())->method('getCustomerGroupIdValues')->willReturn([78]);
        $this->assertSame([78], $this->ruleQuery->getCustomerGroupIdValues());
    }
}
