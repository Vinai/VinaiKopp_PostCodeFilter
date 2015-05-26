<?php

namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\Rule\Components\Country;
use VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\Rule\NonexistentRule;
use VinaiKopp\PostCodeFilter\Storage\ReadModel\RuleReader;
use VinaiKopp\PostCodeFilter\Storage\ReadModel\RuleSpecByCountryAndGroupIds;
use VinaiKopp\PostCodeFilter\Storage\WriteModel\RuleToAdd;
use VinaiKopp\PostCodeFilter\Storage\WriteModel\RuleToDelete;
use VinaiKopp\PostCodeFilter\Storage\WriteModel\RuleWriter;

/**
 * @covers \VinaiKopp\PostCodeFilter\AdminUpdatesRule
 * @uses   \VinaiKopp\PostCodeFilter\Storage\WriteModel\RuleToAdd
 * @uses   \VinaiKopp\PostCodeFilter\Storage\WriteModel\RuleToDelete
 * @uses   \VinaiKopp\PostCodeFilter\Storage\ReadModel\RuleSpecByCountryAndGroupIds
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\Country
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupId
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupIdList
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\PostCode
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\PostCodeList
 */
class AdminUpdatesRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AdminUpdatesRule
     */
    private $useCase;

    /**
     * @var RuleReader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRuleReader;

    /**
     * @var RuleWriter|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRuleWriter;

    /**
     * @return RuleSpecByCountryAndGroupIds|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockRuleToDelete()
    {
        $testGroupIds = [1, 2, 3];
        $mockRule = $this->getMock(RuleToDelete::class, [], [], '', false);
        $mockRule->method('getCountry')
            ->willReturn($this->getMock(Country::class, [], [], '', false));
        $mockRule->method('getCustomerGroupIds')
            ->willReturn($this->getMock(CustomerGroupIdList::class, [], [], '', false));
        $mockRule->method('getCustomerGroupIdValues')->willReturn($testGroupIds);
        return $mockRule;
    }

    /**
     * @return RuleToAdd|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockRuleToAdd()
    {
        return $this->getMock(RuleToAdd::class, [], [], '', false);
    }

    protected function setUp()
    {
        $this->mockRuleWriter = $this->getMock(RuleWriter::class);
        $this->mockRuleReader = $this->getMock(RuleReader::class);
        $this->useCase = new AdminUpdatesRule($this->mockRuleWriter, $this->mockRuleReader);
    }

    /**
     * @test
     */
    public function itShouldOpenAndCommitATransaction()
    {
        $this->mockRuleWriter->expects($this->once())->method('beginTransaction');
        $this->mockRuleWriter->expects($this->once())->method('commitTransaction');
        $this->useCase->updateRule($this->getMockRuleToDelete(), $this->getMockRuleToAdd());
    }

    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exceptions\RuleDoesNotExistException
     */
    public function itShouldThrowIfTheOriginalRuleToUpdateDoesNotExist()
    {
        $this->mockRuleReader->expects($this->once())->method('findByCountryAndGroupIds')
            ->willReturn($this->getMock(NonexistentRule::class, [], [], '', false));
        $this->useCase->updateRule($this->getMockRuleToDelete(), $this->getMockRuleToAdd());
    }

    /**
     * @test
     */
    public function itShouldRollbackTheTransactionIfAnExceptionIsThrown()
    {
        $testException = new \Exception('Test Exception');
        $this->mockRuleReader->expects($this->once())->method('findByCountryAndGroupIds')
            ->willThrowException($testException);
        $this->mockRuleWriter->expects($this->once())->method('rollbackTransaction');
        
        try {
            $this->useCase->updateRule($this->getMockRuleToDelete(), $this->getMockRuleToAdd());
        } catch (\Exception $e) {
            $this->assertSame($testException, $e);
        }
    }

    /**
     * @test
     */
    public function itShouldDeleteTheOldRule()
    {
        $ruleSpecToDelete = $this->getMockRuleToDelete();
        $this->mockRuleWriter->expects($this->once())->method('deleteRule')->with($ruleSpecToDelete);
        $this->useCase->updateRule($ruleSpecToDelete, $this->getMockRuleToAdd());
    }

    /**
     * @test
     */
    public function itShouldAddTheNewRule()
    {
        $mockRuleToAdd = $this->getMockRuleToAdd();
        $this->mockRuleWriter->expects($this->once())->method('createRule')->with($mockRuleToAdd);
        $this->useCase->updateRule($this->getMockRuleToDelete(), $mockRuleToAdd);
    }

    /**
     * @test
     */
    public function itShouldUpdateTheRuleWithScalarInputValues()
    {
        $this->mockRuleWriter->expects($this->once())->method('createRule');
        $oldIso2Country = 'DE';
        $oldCustomerGroupIds = [0, 1];
        $newCountry = 'DE';
        $newCustomerGroupIds = [0, 1, 2];
        $newPostCodes = ['10111', '10123'];
        $this->useCase->updateRuleFromScalars(
            $oldIso2Country,
            $oldCustomerGroupIds,
            $newCountry,
            $newCustomerGroupIds,
            $newPostCodes
        );
    }
}
