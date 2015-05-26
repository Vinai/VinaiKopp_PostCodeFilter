<?php

namespace VinaiKopp\PostCodeFilter\Storage\WriteModel;

use VinaiKopp\PostCodeFilter\Rule\Components\Country;
use VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\Rule\Components\PostCode;
use VinaiKopp\PostCodeFilter\Rule\Components\PostCodeList;

/**
 * @covers \VinaiKopp\PostCodeFilter\Storage\WriteModel\RuleToAdd
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\Country
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupId
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupIdList
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\PostCode
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\PostCodeList
 */
class RuleToAddTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var int[]
     */
    private $testCustomerGroupIds = [1, 2, 3];

    /**
     * @var string[]
     */
    private $testPostCodes = ['123456'];

    /**
     * @var string
     */
    private $testCountry = 'DE';

    /**
     * @var CustomerGroupIdList|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockCustomerGroupIdList;

    /**
     * @var RuleToAdd
     */
    private $ruleToAdd;

    protected function setUp()
    {
        $this->mockCustomerGroupIdList = $this->getMock(CustomerGroupIdList::class, [], [], '', false);

        $stubCountry = $this->getMock(Country::class, [], [], '', false);
        $stubCountry->method('getValue')->willReturn($this->testCountry);

        $stubPostCodeList = $this->getMock(PostCodeList::class, [], [], '', false);
        $stubPostCodeList->method('getValues')->willReturn($this->testPostCodes);
        $stubPostCodeList->method('getPostCodes')->willReturn(
            [$this->getMock(PostCode::class, [], [], '', false)]
        );
        $this->ruleToAdd = new RuleToAdd($this->mockCustomerGroupIdList, $stubCountry, $stubPostCodeList);
    }

    /**
     * @test
     */
    public function itShouldReturnTheCustomerGroupIds()
    {
        $this->assertInstanceOf(CustomerGroupIdList::class, $this->ruleToAdd->getCustomerGroupIds());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCustomerGroupIdValues()
    {
        $this->mockCustomerGroupIdList->expects($this->once())->method('getValues')
            ->willReturn($this->testCustomerGroupIds);
        $this->assertSame($this->testCustomerGroupIds, $this->ruleToAdd->getCustomerGroupIdValues());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCountryInstance()
    {
        $this->assertInstanceOf(Country::class, $this->ruleToAdd->getCountry());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCountryValue()
    {
        $this->assertSame($this->testCountry, $this->ruleToAdd->getCountryValue());
    }

    /**
     * @test
     */
    public function itShouldReturnThePostCodeList()
    {
        $this->assertSame($this->testPostCodes, $this->ruleToAdd->getPostCodeValues());
    }

    /**
     * @test
     */
    public function itShouldCreateAnInstanceFromScalarInput()
    {
        $ruleToAdd = RuleToAdd::createFromScalars(
            $this->testCustomerGroupIds,
            $this->testCountry,
            $this->testPostCodes
        );
        $this->assertInstanceOf(RuleToAdd::class, $ruleToAdd);
    }
}
