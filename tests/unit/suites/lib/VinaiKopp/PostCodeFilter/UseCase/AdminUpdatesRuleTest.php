<?php


namespace VinaiKopp\PostCodeFilter\UseCase;

use VinaiKopp\PostCodeFilter\Country;
use VinaiKopp\PostCodeFilter\CustomerGroupId;
use VinaiKopp\PostCodeFilter\RuleFound;
use VinaiKopp\PostCodeFilter\RuleToUpdate;
use VinaiKopp\PostCodeFilter\RuleNotFound;
use VinaiKopp\PostCodeFilter\RuleRepository;

/**
 * @covers \VinaiKopp\PostCodeFilter\UseCase\AdminUpdatesRule
 * @uses   \VinaiKopp\PostCodeFilter\RuleQuery
 */
class AdminUpdatesRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AdminUpdatesRule
     */
    private $useCase;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|RuleRepository
     */
    private $mockRuleRepository;

    protected function setUp()
    {
        $this->mockRuleRepository = $this->getMock(RuleRepository::class, [], [], '', false);
        $this->useCase = new AdminUpdatesRule($this->mockRuleRepository);
    }

    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exception\RuleDoesNotExistException
     */
    public function itShouldThrowIfTheRuleDoesNotExist()
    {
        $this->mockRuleRepository->expects($this->once())
            ->method('findByGroupAndCountry')
            ->willReturn($this->getMock(RuleNotFound::class));
        
        $this->useCase->updateRule($this->createStubRuleToUpdate());
    }

    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exception\RuleForGroupAndCountryAlreadyExistsException
     */
    public function itShouldThrowIfGroupOrCountryChangedButNewCombinationAlreadyExists()
    {
        $mockRuleToUpdate = $this->createStubRuleToUpdate();
        $mockRuleToUpdate->expects($this->once())
            ->method('isGroupOrCountryChanged')
            ->willReturn(true);
        
        $this->mockRuleRepository->expects($this->exactly(2))
            ->method('findByGroupAndCountry')
            ->willReturnOnConsecutiveCalls(
                $this->getMock(RuleFound::class, [], [], '', false),
                $this->getMock(RuleFound::class, [], [], '', false)
            );
        
        $this->useCase->updateRule($mockRuleToUpdate);
    }

    /**
     * @test
     */
    public function itShouldNotThrowIfGroupOrCountryChangedButNewCombinationNotExists()
    {
        $mockRuleToUpdate = $this->createStubRuleToUpdate();
        $mockRuleToUpdate->expects($this->once())
            ->method('isGroupOrCountryChanged')
            ->willReturn(true);
        
        $this->mockRuleRepository->expects($this->exactly(2))
            ->method('findByGroupAndCountry')
            ->willReturnOnConsecutiveCalls(
                $this->getMock(RuleFound::class, [], [], '', false),
                $this->getMock(RuleNotFound::class, [], [], '', false)
            );
        
        $this->mockRuleRepository->expects($this->once())
            ->method('updateRule')
            ->with($mockRuleToUpdate);
        
        $this->useCase->updateRule($mockRuleToUpdate);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RuleToUpdate
     */
    private function createStubRuleToUpdate()
    {
        $stubRuleToUpdate = $this->getMock(RuleToUpdate::class, [], [], '', false);
        $stubRuleToUpdate->expects($this->any())->method('getOldCustomerGroupId')
            ->willReturn($this->getMock(CustomerGroupId::class, [], [], '', false));
        $stubRuleToUpdate->expects($this->any())->method('getOldCountry')
            ->willReturn($this->getMock(Country::class, [], [], '', false));
        $stubRuleToUpdate->expects($this->any())->method('getNewCustomerGroupId')
            ->willReturn($this->getMock(CustomerGroupId::class, [], [], '', false));
        $stubRuleToUpdate->expects($this->any())->method('getNewCountry')
            ->willReturn($this->getMock(Country::class, [], [], '', false));
        return $stubRuleToUpdate;
    }
}
