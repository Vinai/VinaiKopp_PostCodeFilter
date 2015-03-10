<?php


namespace VinaiKopp\PostCodeFilter;

/**
 * @covers \VinaiKopp\PostCodeFilter\RuleQuery
 */
class RuleQueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RuleQuery
     */
    private $ruleQuery;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|CustomerGroupId
     */
    private $stubCustomerGroupId;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Country
     */
    private $stubCountry;

    public function setUp()
    {
        $this->stubCustomerGroupId = $this->getMock(CustomerGroupId::class, [], [], '', false);
        $this->stubCountry = $this->getMock(Country::class, [], [], '', false);
        $this->ruleQuery = new RuleQuery($this->stubCustomerGroupId, $this->stubCountry);
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
    public function itShouldReturnTheCountryValue()
    {
        $countryValue = 'DE';
        $this->stubCountry->expects($this->once())->method('getValue')->willReturn($countryValue);
        $this->assertSame($countryValue, $this->ruleQuery->getCountryValue());
    }
}
