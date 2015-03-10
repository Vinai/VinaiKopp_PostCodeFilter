<?php


namespace VinaiKopp\PostCodeFilter\UseCase;


use VinaiKopp\PostCodeFilter\Country;
use VinaiKopp\PostCodeFilter\CustomerGroupId;
use VinaiKopp\PostCodeFilter\RuleFound;
use VinaiKopp\PostCodeFilter\RuleToAdd;
use VinaiKopp\PostCodeFilter\RuleRepository;

/**
 * @covers \VinaiKopp\PostCodeFilter\UseCase\AdminAddsRule
 * @uses   \VinaiKopp\PostCodeFilter\RuleFound
 * @uses   \VinaiKopp\PostCodeFilter\RuleQuery
 */
class AdminAddsRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AdminAddsRule
     */
    private $useCase;

    /**
     * @var RuleRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRuleRepository;

    protected function setUp()
    {
        $this->mockRuleRepository = $this->getMock(RuleRepository::class, [], [], '', false);
        $this->useCase = new AdminAddsRule($this->mockRuleRepository);
    }

    /**
     * @test
     */
    public function itShouldDelegateToTheRepository()
    {
        $stubRuleToAdd = $this->createStubRuleToAdd();
        $this->mockRuleRepository->expects($this->once())
            ->method('findByGroupAndCountry');
        $this->mockRuleRepository->expects($this->once())
            ->method('createRule')
            ->with($stubRuleToAdd);

        $this->useCase->addRule($stubRuleToAdd);
    }

    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exception\RuleAlreadyExistsException
     */
    public function itShouldThrowIfRuleForGroupAndCountryAlreadyExist()
    {
        $this->mockRuleRepository->expects($this->once())
            ->method('findByGroupAndCountry')
            ->willReturn($this->getMock(RuleFound::class, [], [[]]));

        $stubRuleToAdd = $this->createStubRuleToAdd();   
        $this->mockRuleRepository->expects($this->never())
            ->method('createRule')
            ->with($stubRuleToAdd);

        $this->useCase->addRule($stubRuleToAdd);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RuleToAdd
     */
    private function createStubRuleToAdd()
    {
        $stubRuleToAdd = $this->getMock(RuleToAdd::class, [], [], '', false);
        $stubRuleToAdd->expects($this->any())->method('getCustomerGroupId')->willReturn(
            $this->getMock(CustomerGroupId::class, [], [], '', false)
        );
        $stubRuleToAdd->expects($this->any())->method('getCountry')->willReturn(
            $this->getMock(Country::class, [], [], '', false)
        );
        return $stubRuleToAdd;
    }
}
