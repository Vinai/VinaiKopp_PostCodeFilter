<?php

namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\Rule\Components\Country;
use VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\Rule\NonexistentRule;
use VinaiKopp\PostCodeFilter\Storage\ReadModel\RuleReader;
use VinaiKopp\PostCodeFilter\Storage\WriteModel\RuleToDelete;
use VinaiKopp\PostCodeFilter\Storage\WriteModel\RuleWriter;

/**
 * @covers \VinaiKopp\PostCodeFilter\AdminDeletesRule
 * @uses   \VinaiKopp\PostCodeFilter\Storage\WriteModel\RuleToDelete
 * @uses   \VinaiKopp\PostCodeFilter\Storage\ReadModel\RuleSpecByCountryAndGroupIds
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\Country
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupId
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupIdList
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
            ->willReturn($this->getMock(NonexistentRule::class, [], [], '', false));
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
        $stubRuleToDelete->method('getCustomerGroupIds')
            ->willReturn($this->getMock(CustomerGroupIdList::class, [], [], '', false));
        $stubRuleToDelete->method('getCustomerGroupIdValues')
            ->willReturn([1, 2]);
        $stubRuleToDelete->method('getCountry')
            ->willReturn($this->getMock(Country::class, [], [], '', false));
        return $stubRuleToDelete;
    }
}
