<?php


namespace VinaiKopp\PostCodeFilter;

/**
 * @covers \VinaiKopp\PostCodeFilter\RuleToAdd
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
    public function itShouldReturnTheCustomerGroupIdValue()
    {
        $this->assertSame($this->testCustomerGroupId, $this->ruleToAdd->getCustomerGroupIdValue());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCustomerGroupId()
    {
        $this->assertSame($this->testCustomerGroupId, $this->ruleToAdd->getCustomerGroupIdValue());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCountryCode()
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
