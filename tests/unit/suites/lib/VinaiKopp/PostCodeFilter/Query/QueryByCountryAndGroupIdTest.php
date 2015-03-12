<?php


namespace VinaiKopp\PostCodeFilter\Query;

use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId;

/**
 * @covers \VinaiKopp\PostCodeFilter\Query\QueryByCountryAndGroupId
 */
class QueryByCountryAndGroupIdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var QueryByCountryAndGroupId
     */
    private $ruleQuery;

    /**
     * @var CustomerGroupId|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubCustomerGroupId;

    /**
     * @var Country|\PHPUnit_Framework_MockObject_MockObject
     */
    private $stubCountry;

    public function setUp()
    {
        $this->stubCustomerGroupId = $this->getMock(CustomerGroupId::class, [], [], '', false);
        $this->stubCountry = $this->getMock(Country::class, [], [], '', false);
        $this->ruleQuery = new QueryByCountryAndGroupId($this->stubCustomerGroupId, $this->stubCountry);
    }

    /**
     * @test
     */
    public function itShouldReturnTheCustomerGroupIdInstance()
    {
        $this->assertSame($this->stubCustomerGroupId, $this->ruleQuery->getCustomerGroupId());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCustomerGroupIdValue()
    {
        $groupIdValue = 2;
        $this->stubCustomerGroupId->expects($this->once())->method('getValue')->willReturn($groupIdValue);
        $this->assertSame($groupIdValue, $this->ruleQuery->getCustomerGroupIdValue());
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
}
