<?php


namespace VinaiKopp\PostCodeFilter\UseCases;


use VinaiKopp\PostCodeFilter\Query\RuleFound;
use VinaiKopp\PostCodeFilter\Query\RuleReader;
use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\Command\RuleToAdd;
use VinaiKopp\PostCodeFilter\Command\RuleWriter;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;

/**
 * @covers \VinaiKopp\PostCodeFilter\UseCases\AdminAddsRule
 * @uses   \VinaiKopp\PostCodeFilter\Query\RuleFound
 * @uses   \VinaiKopp\PostCodeFilter\Query\QueryByCountryAndGroupIds
 */
class AdminAddsRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AdminAddsRule
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
        $this->useCase = new AdminAddsRule($this->mockRuleWriter, $this->mockRuleReader);
    }

    /**
     * @test
     */
    public function itShouldDelegateToTheRepository()
    {
        $stubRuleToAdd = $this->createStubRuleToAdd();
        $this->mockRuleReader->expects($this->once())
            ->method('findByCountryAndGroupIds');
        $this->mockRuleWriter->expects($this->once())
            ->method('createRule')
            ->with($stubRuleToAdd);

        $this->useCase->addRule($stubRuleToAdd);
    }

    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exceptions\RuleAlreadyExistsException
     */
    public function itShouldThrowIfRuleForGroupAndCountryAlreadyExist()
    {
        $this->mockRuleReader->expects($this->once())
            ->method('findByCountryAndGroupIds')
            ->willReturn($this->getMock(RuleFound::class, [], [], '', false));

        $stubRuleToAdd = $this->createStubRuleToAdd();   
        $this->mockRuleWriter->expects($this->never())
            ->method('createRule')
            ->with($stubRuleToAdd);

        $this->useCase->addRule($stubRuleToAdd);
    }

    /**
     * @return RuleToAdd|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createStubRuleToAdd()
    {
        $stubRuleToAdd = $this->getMock(RuleToAdd::class, [], [], '', false);
        $stubRuleToAdd->expects($this->any())->method('getCustomerGroupIds')->willReturn(
            $this->getMock(CustomerGroupIdList::class, [], [], '', false)
        );
        $stubRuleToAdd->expects($this->any())->method('getCountry')->willReturn(
            $this->getMock(Country::class, [], [], '', false)
        );
        return $stubRuleToAdd;
    }
}
