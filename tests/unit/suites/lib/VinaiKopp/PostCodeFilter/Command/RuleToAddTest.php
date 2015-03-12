<?php


namespace VinaiKopp\PostCodeFilter\Command;

use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId;
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
     * @var int
     */
    private $testCustomerGroupId = 15;

    /**
     * @var string
     */
    private $testCountry = 'DE';
    
    /**
     * @var RuleToAdd
     */
    private $ruleToAdd;

    protected function setUp()
    {
        $stubCustomerGroupId = $this->getMock(CustomerGroupId::class, [], [], '', false);
        $stubCustomerGroupId->expects($this->any())->method('getValue')->willReturn($this->testCustomerGroupId);
        
        $stubCountry = $this->getMock(Country::class, [], [], '', false);
        $stubCountry->expects($this->any())->method('getValue')->willReturn($this->testCountry);
        
        $stubPostCodeList = $this->getMock(PostCodeList::class, [], [], '', false);
        $stubPostCodeList->expects($this->any())->method('getValues')->willReturn($this->testPostCodes);
        $stubPostCodeList->expects($this->any())->method('getPostCodes')->willReturn(
            [$this->getMock(PostCode::class, [], [], '', false)]
        );
        $this->ruleToAdd = new RuleToAdd($stubCustomerGroupId, $stubCountry, $stubPostCodeList);
    }

    /**
     * @test
     */
    public function itShouldReturnTheCustomerGroupId()
    {
        $this->assertInstanceOf(CustomerGroupId::class, $this->ruleToAdd->getCustomerGroupId());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCustomerGroupIdValue()
    {
        $this->assertSame($this->testCustomerGroupId, $this->ruleToAdd->getCustomerGroupIdValue());
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
