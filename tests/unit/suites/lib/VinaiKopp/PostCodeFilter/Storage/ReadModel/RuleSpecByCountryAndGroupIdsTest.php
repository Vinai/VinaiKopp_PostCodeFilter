<?php

namespace VinaiKopp\PostCodeFilter\Storage\ReadModel;

use VinaiKopp\PostCodeFilter\Rule\Components\Country;
use VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupId;
use VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupIdList;

/**
 * @covers \VinaiKopp\PostCodeFilter\Storage\ReadModel\RuleSpecByCountryAndGroupIds
 */
class RuleSpecByCountryAndGroupIdsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RuleSpecByCountryAndGroupIds
     */
    private $ruleSpec;

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
        $this->ruleSpec = new RuleSpecByCountryAndGroupIds($this->stubCountry, $this->stubCustomerGroupIdList);
    }

    /**
     * @test
     */
    public function itShouldReturnTheCountryInstance()
    {
        $this->assertSame($this->stubCountry, $this->ruleSpec->getCountry());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCountryValue()
    {
        $countryValue = 'DE';
        $this->stubCountry->expects($this->once())->method('getValue')->willReturn($countryValue);
        $this->assertSame($countryValue, $this->ruleSpec->getCountryValue());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCustomerGroupIdInstances()
    {
        $this->assertSame($this->stubCustomerGroupIdList, $this->ruleSpec->getCustomerGroupIds());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCustomerGroupIdValues()
    {
        $this->stubCustomerGroupIdList->expects($this->once())->method('getValues')->willReturn([78]);
        $this->assertSame([78], $this->ruleSpec->getCustomerGroupIdValues());
    }
}
