<?php


namespace VinaiKopp\PostCodeFilter\UseCase;

use VinaiKopp\PostCodeFilter\Country;
use VinaiKopp\PostCodeFilter\CustomerGroupId;
use VinaiKopp\PostCodeFilter\RuleNotFound;
use VinaiKopp\PostCodeFilter\RuleRepository;
use VinaiKopp\PostCodeFilter\RuleToDelete;

/**
 * @covers \VinaiKopp\PostCodeFilter\UseCase\AdminDeletesRule
 * @uses   \VinaiKopp\PostCodeFilter\RuleQuery
 */
class AdminDeletesRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AdminDeletesRule
     */
    private $useCase;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RuleRepository
     */
    private $mockRuleRepository;

    protected function setUp()
    {
        $this->mockRuleRepository = $this->getMock(RuleRepository::class, [], [], '', false);
        $this->useCase = new AdminDeletesRule($this->mockRuleRepository);
    }

    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exception\RuleDoesNotExistException
     */
    public function itShouldThrowIfTheRuleDoesNotExist()
    {
        $stubRule = $this->createStubRuleToDelete();
        $this->mockRuleRepository->expects($this->once())->method('findByGroupAndCountry')
            ->willReturn($this->getMock(RuleNotFound::class));
        $this->useCase->deleteRule($stubRule);
    }

    /**
     * @test
     */
    public function itShouldDelegateToTheStorageToDelete()
    {
        $stubRule = $this->createStubRuleToDelete();
        $this->mockRuleRepository->expects($this->once())->method('deleteRule')->with($stubRule);
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
