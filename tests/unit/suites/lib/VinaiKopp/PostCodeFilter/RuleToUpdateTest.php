<?php


namespace VinaiKopp\PostCodeFilter;

/**
 * @covers \VinaiKopp\PostCodeFilter\RuleToUpdate
 */
class RuleToUpdateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var int
     */
    private $testOldCustomerGroupId = 4;

    /**
     * @var string
     */
    private $testOldCountry = 'DE';

    /**
     * @var RuleToAdd|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockNewRule;

    /**
     * @var RuleToUpdate
     */
    private $ruleToUpdate;

    protected function setUp()
    {
        $stubOldCustomerGroupId = $this->getMock(CustomerGroupId::class, [], [], '', false);
        $stubOldCustomerGroupId->expects($this->any())->method('getValue')->willReturn($this->testOldCustomerGroupId);

        $stubCountry = $this->getMock(Country::class, [], [], '', false);
        $stubCountry->expects($this->any())->method('getValue')->willReturn($this->testOldCountry);

        $this->mockNewRule = $this->getMock(RuleToAdd::class, [], [], '', false);

        $this->ruleToUpdate = new RuleToUpdate($stubOldCustomerGroupId, $stubCountry, $this->mockNewRule);
    }

    /**
     * @test
     */
    public function itShouldReturnTheOldCustomerGroupIdInstance()
    {
        $this->assertInstanceOf(CustomerGroupId::class, $this->ruleToUpdate->getOldCustomerGroupId());
    }

    /**
     * @test
     */
    public function itShouldReturnTheOldCustomerGroupIdValue()
    {
        $this->assertSame($this->testOldCustomerGroupId, $this->ruleToUpdate->getOldCustomerGroupIdValue());
    }

    /**
     * @test
     */
    public function itShouldReturnTheOldCountryInstance()
    {
        $this->assertInstanceOf(Country::class, $this->ruleToUpdate->getOldCountry());
    }

    /**
     * @test
     */
    public function itShouldReturnTheOldCountryValue()
    {
        $this->assertSame($this->testOldCountry, $this->ruleToUpdate->getOldCountryValue());
    }

    /**
     * @test
     */
    public function itShouldReturnTheNewCustomerGroupId()
    {
        $this->mockNewRule->expects($this->once())->method('getCustomerGroupId')->willReturn('test');
        $this->assertEquals('test', $this->ruleToUpdate->getNewCustomerGroupId());
    }

    /**
     * @test
     */
    public function itShouldReturnTheNewCustomerGroupIdValue()
    {
        $this->mockNewRule->expects($this->once())
            ->method('getCustomerGroupIdValue')
            ->willReturn(5);
        $this->assertSame(5, $this->ruleToUpdate->getNewCustomerGroupIdValue());
    }

    /**
     * @test
     */
    public function itShouldReturnTheNewCountry()
    {
        $this->mockNewRule->expects($this->once())->method('getCountry')->willReturn('test');
        $this->assertEquals('test', $this->ruleToUpdate->getNewCountry());
    }

    /**
     * @test
     */
    public function itShouldReturnTheNewCountryValue()
    {
        $this->mockNewRule->expects($this->once())
            ->method('getCountryValue')
            ->willReturn('XY');
        $this->assertSame('XY', $this->ruleToUpdate->getNewCountryValue());
    }

    /**
     * @test
     */
    public function itShouldReturnTheNewPostCodeValues()
    {
        $this->mockNewRule->expects($this->once())
            ->method('getPostCodeValues')
            ->willReturn([]);
        $this->assertSame([], $this->ruleToUpdate->getNewPostCodeValues());
    }

    /**
     * @test
     * @dataProvider newCountryAndGroupIdProvider
     */
    public function itShouldReturnTrueIfGroupOrCountryWasChanged($newCountry, $newCustomerGroupId)
    {
        $this->mockNewRule->expects($this->any())->method('getCountryValue')->willReturn($newCountry);
        $this->mockNewRule->expects($this->any())->method('getCustomerGroupIdValue')->willReturn($newCustomerGroupId);
        $this->assertTrue($this->ruleToUpdate->isGroupOrCountryChanged());
    }

    /**
     * @return array[]
     */
    public function newCountryAndGroupIdProvider()
    {
        return [
            [$this->testOldCountry, $this->testOldCustomerGroupId + 1],
            ['ZZ', $this->testOldCustomerGroupId],
            ['ZZ', $this->testOldCustomerGroupId + 1],
        ];
    }

    /**
     * @test
     */
    public function itShouldReturnFalseIfTheGroupAndCountryAreUnchanged()
    {
        $this->mockNewRule->expects($this->any())->method('getCountryValue')->willReturn($this->testOldCountry);
        $this->mockNewRule->expects($this->any())->method('getCustomerGroupIdValue')->willReturn($this->testOldCustomerGroupId);
        $this->assertFalse($this->ruleToUpdate->isGroupOrCountryChanged());
    }
}
