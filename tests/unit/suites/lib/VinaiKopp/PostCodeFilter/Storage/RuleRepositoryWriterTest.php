<?php

namespace VinaiKopp\PostCodeFilter\Storage;

use VinaiKopp\PostCodeFilter\Storage\WriteModel\RuleToAdd;
use VinaiKopp\PostCodeFilter\Storage\WriteModel\RuleToDelete;
use VinaiKopp\PostCodeFilter\Storage\WriteModel\RuleWriter;

/**
 * @covers \VinaiKopp\PostCodeFilter\Storage\RuleRepositoryWriter
 */
class RuleRepositoryWriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RuleRepositoryWriter
     */
    private $repository;

    /**
     * @var int
     */
    private $testGroupId = 5;

    /**
     * @var string
     */
    private $testCountry = 'DE';

    /**
     * @var string[]
     */
    private $testPostCodes = ['123456'];

    /**
     * @var RuleStorage|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockStorage;

    /**
     * @param mixed $groupId
     * @param mixed $country
     * @param mixed $postCodes
     * @return RuleToAdd|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createMockRuleToAdd($groupId, $country, $postCodes)
    {
        $stubRuleToAdd = $this->getMock(RuleToAdd::class, [], [], '', false);
        $stubRuleToAdd->method('getCustomerGroupIdValues')->willReturn([$groupId]);
        $stubRuleToAdd->method('getCountryValue')->willReturn($country);
        $stubRuleToAdd->method('getPostCodeValues')->willReturn($postCodes);
        return $stubRuleToAdd;
    }

    /**
     * @return RuleToDelete|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createMockRuleToDelete()
    {
        $mockRuleToDelete = $this->getMock(RuleToDelete::class, [], [], '', false);
        $mockRuleToDelete->method('getCustomerGroupIdValues')->willReturn([$this->testGroupId]);
        $mockRuleToDelete->method('getCountryValue')->willReturn($this->testCountry);
        return $mockRuleToDelete;
    }

    protected function setUp()
    {
        $this->mockStorage = $this->getMock(RuleStorage::class);
        $this->repository = new RuleRepositoryWriter($this->mockStorage);
    }

    /**
     * @test
     */
    public function itShouldImplementRuleWriter()
    {
        $this->assertInstanceOf(RuleWriter::class, $this->repository);
    }

    /**
     * @test
     */
    public function itShouldDelegateInsertsToTheStorage()
    {
        $stubRuleToAdd = $this->createMockRuleToAdd($this->testGroupId, $this->testCountry, $this->testPostCodes);
        $this->mockStorage->expects($this->once())->method('create')->with(
            $this->testCountry,
            $this->testGroupId,
            $this->testPostCodes
        );

        $this->repository->createRule($stubRuleToAdd);
    }

    /**
     * @test
     */
    public function itShouldDelegateDeletesToTheStorage()
    {
        $stubRuleToDelete = $this->createMockRuleToDelete();
        $this->mockStorage->expects($this->once())->method('delete')->with($this->testCountry, $this->testGroupId);
        $this->repository->deleteRule($stubRuleToDelete);
    }

    /**
     * @test
     */
    public function itShouldDelegateToTheStorageToOpenATransaction()
    {
        $this->mockStorage->expects($this->once())->method('beginTransaction');
        $this->repository->beginTransaction();
    }

    /**
     * @test
     */
    public function itShouldDelegateToTheStorageToCommitATransaction()
    {
        $this->mockStorage->expects($this->once())->method('commitTransaction');
        $this->repository->commitTransaction();
    }

    /**
     * @test
     */
    public function itShouldDelegateToTheStorageToAbortATransaction()
    {
        $this->mockStorage->expects($this->once())->method('rollbackTransaction');
        $this->repository->rollbackTransaction();
    }
}
