<?php


namespace VinaiKopp\PostCodeFilter\Command;


use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\RuleComponents\PostCodeList;

/**
 * @covers \VinaiKopp\PostCodeFilter\Command\RuleToUpdate
 */
class RuleToUpdateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RuleToUpdate
     */
    private $ruleToUpdate;

    /**
     * @var Country|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockOldCountry;

    /**
     * @var CustomerGroupIdList|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockOldCustomerGroupIds;

    /**
     * @var Country|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockNewCountry;

    /**
     * @var CustomerGroupIdList|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockNewCustomerGroupIds;

    /**
     * @var PostCodeList|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockNewPostCodes;

    protected function setUp()
    {
        $this->mockOldCountry = $this->getMock(Country::class, [], [], '', false);
        $this->mockOldCustomerGroupIds = $this->getMock(CustomerGroupIdList::class, [], [], '', false);
        $this->mockNewCountry = $this->getMock(Country::class, [], [], '', false);
        $this->mockNewCustomerGroupIds = $this->getMock(CustomerGroupIdList::class, [], [], '', false);
        $this->mockNewPostCodes = $this->getMock(PostCodeList::class, [], [], '', false);
        
        $this->ruleToUpdate = new RuleToUpdate(
            $this->mockOldCountry,
            $this->mockOldCustomerGroupIds,
            $this->mockNewCountry,
            $this->mockNewCustomerGroupIds,
            $this->mockNewPostCodes
        );
    }

    /**
     * @test
     */
    public function itShouldReturnTheOldCountry()
    {
        $this->assertSame($this->mockOldCountry, $this->ruleToUpdate->getOldCountry());
    }

    /**
     * @test
     */
    public function itShouldReturnTheOldCountryValue()
    {
        $testCountryCode = 'XX';
        $this->mockOldCountry->expects($this->once())->method('getValue')->willReturn($testCountryCode);
        $this->assertSame($testCountryCode, $this->ruleToUpdate->getOldCountryValue());
    }

    /**
     * @test
     */
    public function itShouldReturnTheOldCustomerGroupIdList()
    {
        $this->assertSame($this->mockOldCustomerGroupIds, $this->ruleToUpdate->getOldCustomerGroupIds());
    }

    /**
     * @test
     */
    public function itShouldReturnTheOldCustomerGroupIdValues()
    {
        $testGroupIds = [1, 2];
        $this->mockOldCustomerGroupIds->expects($this->once())->method('getValues')->willReturn($testGroupIds);
        $this->assertSame($testGroupIds, $this->ruleToUpdate->getOldCustomerGroupIdValues());
    }

    /**
     * @test
     */
    public function itShouldReturnTheNewCountry()
    {
        $this->assertSame($this->mockNewCountry, $this->ruleToUpdate->getNewCountry());
    }

    /**
     * @test
     */
    public function itShouldReturnTheNewCountryValue()
    {
        $testCountryCode = 'QQ';
        $this->mockNewCountry->expects($this->once())->method('getValue')->willReturn($testCountryCode);
        $this->assertSame($testCountryCode, $this->ruleToUpdate->getNewCountryValue());
    }

    /**
     * @test
     */
    public function itShouldReturnTheNewCustomerGroupIdList()
    {
        $this->assertSame($this->mockNewCustomerGroupIds, $this->ruleToUpdate->getNewCustomerGroupIds());
    }

    /**
     * @test
     */
    public function itShouldReturnTheNewCustomerGroupIdListValues()
    {
        $testGroupIds = [1, 2];
        $this->mockNewCustomerGroupIds->expects($this->once())->method('getValues')->willReturn($testGroupIds);
        $this->assertSame($testGroupIds, $this->ruleToUpdate->getNewCustomerGroupIdValues());
    }

    /**
     * @test
     */
    public function itShouldReturnTheNewPostCodeList()
    {
        $this->assertSame($this->mockNewPostCodes, $this->ruleToUpdate->getNewPostCodes());
    }

    /**
     * @test
     */
    public function itShouldReturnTheNewPostCodeListValues()
    {
        $testPostCodes = ['aaa', 'bbb'];
        $this->mockNewPostCodes->expects($this->once())->method('getValues')->willReturn($testPostCodes);
        $this->assertSame($testPostCodes, $this->ruleToUpdate->getNewPostCodeValues());
    }
}
