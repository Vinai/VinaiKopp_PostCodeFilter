<?php


namespace VinaiKopp\PostCodeFilter\UseCases;

use VinaiKopp\PostCodeFilter\Query\RuleNotFound;
use VinaiKopp\PostCodeFilter\Query\RuleReader;
use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId;
use VinaiKopp\PostCodeFilter\Command\RuleToDelete;
use VinaiKopp\PostCodeFilter\Command\RuleWriter;

/**
 * @covers \VinaiKopp\PostCodeFilter\UseCases\AdminDeletesRule
 * @uses   \VinaiKopp\PostCodeFilter\Query\QueryByCountryAndGroupId
 */
class AdminDeletesRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AdminDeletesRule
     */
    private $useCase;

    /**
     * @var RuleWriter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRuleWriter;

    /**
     * @var RuleReader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRuleReader;

    protected function setUp()
    {
        $this->mockRuleWriter = $this->getMock(RuleWriter::class);
        $this->mockRuleReader = $this->getMock(RuleReader::class);
        $this->useCase = new AdminDeletesRule($this->mockRuleWriter, $this->mockRuleReader);
    }

    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exceptions\RuleDoesNotExistException
     */
    public function itShouldThrowIfTheRuleDoesNotExist()
    {
        $stubRule = $this->createStubRuleToDelete();
        $this->mockRuleReader->expects($this->once())->method('findByCountryAndGroupId')
            ->willReturn($this->getMock(RuleNotFound::class, [], [], '', false));
        $this->useCase->deleteRule($stubRule);
    }

    /**
     * @test
     */
    public function itShouldDelegateToTheStorageToDelete()
    {
        $stubRule = $this->createStubRuleToDelete();
        $this->mockRuleWriter->expects($this->once())->method('deleteRule')->with($stubRule);
        $this->useCase->deleteRule($stubRule);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createStubRuleToDelete()
    {
        $stubRuleToDelete = $this->getMock(RuleToDelete::class, [], [], '', false);
        $stubRuleToDelete->expects($this->any())->method('getCustomerGroupId')
            ->willReturn($this->getMock(CustomerGroupId::class, [], [], '', false));
        $stubRuleToDelete->expects($this->any())->method('getCountry')
            ->willReturn($this->getMock(Country::class, [], [], '', false));
        return $stubRuleToDelete;
    }
}
