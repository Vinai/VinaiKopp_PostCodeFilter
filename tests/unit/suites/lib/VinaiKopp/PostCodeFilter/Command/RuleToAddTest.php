<?php


namespace VinaiKopp\PostCodeFilter\Command;

use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\RuleComponents\PostCode;
use VinaiKopp\PostCodeFilter\RuleComponents\PostCodeList;

/**
 * @covers \VinaiKopp\PostCodeFilter\Command\RuleToAdd
 */
class RuleToAddTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var mixed[]
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
        $stubCountry->expects($this->any())->method('getValue')->willReturn($this->testCountry);
        
        $stubPostCodeList = $this->getMock(PostCodeList::class, [], [], '', false);
        $stubPostCodeList->expects($this->any())->method('getValues')->willReturn($this->testPostCodes);
        $stubPostCodeList->expects($this->any())->method('getPostCodes')->willReturn(
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
        $this->mockCustomerGroupIdList->expects($this->once())->method('getValues')->willReturn([1, 2, 3]);
        $this->assertSame([1, 2, 3], $this->ruleToAdd->getCustomerGroupIdValues());
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
}
