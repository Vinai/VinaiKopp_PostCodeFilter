<?php


namespace VinaiKopp\PostCodeFilter\UseCases;

use VinaiKopp\PostCodeFilter\Query\RuleNotFound;
use VinaiKopp\PostCodeFilter\Query\RuleReader;
use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\Command\RuleToDelete;
use VinaiKopp\PostCodeFilter\Command\RuleWriter;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;

/**
 * @covers \VinaiKopp\PostCodeFilter\UseCases\AdminDeletesRule
 * @uses   \VinaiKopp\PostCodeFilter\Command\RuleToDelete
 * @uses   \VinaiKopp\PostCodeFilter\Query\RuleSpecByCountryAndGroupIds
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\Country
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList
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
        $this->mockRuleReader->expects($this->once())->method('findByCountryAndGroupIds')
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
     * @test
     */
    public function itShouldOpenAndCommitATransaction()
    {
        $this->mockRuleWriter->expects($this->once())->method('beginTransaction');
        $this->useCase->deleteRule($this->createStubRuleToDelete());
    }

    /**
     * @test
     */
    public function itShouldRollBackTheTransactionIfAnExceptionIsThrown()
    {
        $testException = new \Exception('Test Exception');
        $this->mockRuleReader->expects($this->once())->method('findByCountryAndGroupIds')
            ->willThrowException($testException);
        $this->mockRuleWriter->expects($this->once())->method('rollbackTransaction');

        try {
            $this->useCase->deleteRule($this->createStubRuleToDelete());
        } catch (\Exception $e) {
            $this->assertSame($testException, $e);
        }
    }

    /**
     * @test
     */
    public function itShouldDeleteARuleFromScalarValues()
    {
        $this->mockRuleWriter->expects($this->once())->method('deleteRule');
        $this->useCase->deleteRuleFromScalars([0, 1, 2], 'US');
    }

    /**
     * @return RuleToDelete|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createStubRuleToDelete()
    {
        $stubRuleToDelete = $this->getMock(RuleToDelete::class, [], [], '', false);
        $stubRuleToDelete->expects($this->any())->method('getCustomerGroupIds')
            ->willReturn($this->getMock(CustomerGroupIdList::class, [], [], '', false));
        $stubRuleToDelete->expects($this->any())->method('getCustomerGroupIdValues')
            ->willReturn([1, 2]);
        $stubRuleToDelete->expects($this->any())->method('getCountry')
            ->willReturn($this->getMock(Country::class, [], [], '', false));
        return $stubRuleToDelete;
    }
}
