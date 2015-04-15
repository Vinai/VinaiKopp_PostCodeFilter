<?php

namespace VinaiKopp\PostCodeFilter\Query;

use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\RuleComponents\PostCodeList;

/**
 * @covers \VinaiKopp\PostCodeFilter\Query\RuleNotFound
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\PostCodeList
 */
class RuleNotFoundTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RuleNotFound
     */
    private $rule;

    /**
     * @var Country|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockCountry;

    protected function setUp()
    {
        $this->mockCountry = $this->getMock(Country::class, [], [], '', false); 
        $this->rule = new RuleNotFound($this->mockCountry);
    }

    /**
     * @test
     */
    public function itShouldBeARule()
    {
        $this->assertInstanceOf(RuleResult::class, $this->rule);
    }

    /**
     * @test
     */
    public function itShouldReturnTrueForAnyPostCode()
    {
        $this->assertTrue($this->rule->isPostCodeAllowed('dummy'));
    }

    /**
     * @test
     */
    public function itShouldReturnTheCountry()
    {
        $this->assertInstanceOf(Country::class, $this->rule->getCountry());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCountryValue()
    {
        $this->mockCountry->expects($this->once())->method('getValue')->willReturn('DE');
        $this->assertEquals('DE', $this->rule->getCountryValue());
    }

    /**
     * @test
     */
    public function itShouldReturnNoCustomerGroupIds()
    {
        $customerGroupIdList = $this->rule->getCustomerGroupIds();
        $this->assertInstanceOf(CustomerGroupIdList::class, $customerGroupIdList);
        $this->assertSame([], $customerGroupIdList->getCustomerGroupIds());
    }

    /**
     * @test
     */
    public function itShouldReturnNoCustomerGroupIdValues()
    {
        $this->assertSame([], $this->rule->getCustomerGroupIdValues());
    }

    /**
     * @test
     */
    public function itShouldReturnAnEmptyPostCodeList()
    {
        $result = $this->rule->getPostCodes();
        $this->assertInstanceOf(PostCodeList::class, $result);
        $this->assertSame([], $result->getValues());
    }

    /**
     * @test
     */
    public function itShouldReturnNoPostCodeValues()
    {
        $this->assertSame([], $this->rule->getPostCodeValues());
    }
}
