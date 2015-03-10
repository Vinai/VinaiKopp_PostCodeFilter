<?php


namespace VinaiKopp\PostCodeFilter;

/**
 * @covers \VinaiKopp\PostCodeFilter\RuleRepository
 * @uses   \VinaiKopp\PostCodeFilter\RuleNotFound
 * @uses   \VinaiKopp\PostCodeFilter\RuleFound
 */
class RuleRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RuleRepository
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

    protected function setUp()
    {
        $this->mockStorage = $this->getMock(RuleStorage::class);
        $this->repository = new RuleRepository($this->mockStorage);
    }

    /**
     * @test
     */
    public function itShouldReturnARuleFromAQuery()
    {
        $result = $this->repository->findByGroupAndCountry($this->createMockRuleQuery());
        $this->assertInstanceOf(Rule::class, $result);
    }

    /**
     * @test
     */
    public function itShouldReturnANotFoundRuleIfTheStorageReturnsNoMatches()
    {
        $this->mockStorage->expects($this->once())->method('findPostCodesByGroupAndCountry')
            ->with($this->testGroupId, $this->testCountry)
            ->willReturn([]);
        $result = $this->repository->findByGroupAndCountry($this->createMockRuleQuery());
        $this->assertInstanceOf(RuleNotFound::class, $result);
    }

    /**
     * @test
     */
    public function itShouldReturnAFoundRuleIfTheStorageReturnsAMatch()
    {
        $this->mockStorage->expects($this->once())->method('findPostCodesByGroupAndCountry')
            ->with($this->testGroupId, $this->testCountry)
            ->willReturn(['123']);
        $result = $this->repository->findByGroupAndCountry($this->createMockRuleQuery());
        $this->assertInstanceOf(RuleFound::class, $result);
    }

    /**
     * @test
     */
    public function itShouldDelegateInsertsToTheStorage()
    {
        $stubRuleToAdd = $this->createMockRuleToAdd($this->testGroupId, $this->testCountry, $this->testPostCodes);
        $this->mockStorage->expects($this->once())->method('create')->with($stubRuleToAdd);

        $this->repository->createRule($stubRuleToAdd);
    }

    /**
     * @test
     */
    public function itShouldDelegateUpdatesToTheStorage()
    {
        $stubRuleToUpdate = $this->createMockRuleToUpdate(
            $this->testGroupId,
            $this->testCountry,
            $this->testGroupId,
            $this->testCountry,
            $this->testPostCodes
        );
        $this->mockStorage->expects($this->once())->method('update')->with($stubRuleToUpdate);

        $this->repository->updateRule($stubRuleToUpdate);
    }

    /**
     * @test
     */
    public function itShouldDelegateDeletesToTheStorage()
    {
        $stubRuleToDelete = $this->createMockRuleToDelete();
        $this->mockStorage->expects($this->once())->method('delete')->with($stubRuleToDelete);
        $this->repository->deleteRule($stubRuleToDelete);
    }

    /**
     * @param mixed $groupId
     * @param mixed $country
     * @param mixed $postCodes
     * @return \PHPUnit_Framework_MockObject_MockObject|RuleToAdd
     */
    private function createMockRuleToAdd($groupId, $country, $postCodes)
    {
        $stubRuleToAdd = $this->getMock(RuleToAdd::class, [], [], '', false);
        $stubRuleToAdd->expects($this->any())->method('getCustomerGroupId')->willReturn($groupId);
        $stubRuleToAdd->expects($this->any())->method('getCountry')->willReturn($country);
        $stubRuleToAdd->expects($this->any())->method('getPostCodes')->willReturn($postCodes);
        return $stubRuleToAdd;
    }

    /**
     * @param mixed $oldGroupId
     * @param mixed $oldCountry
     * @param mixed $newGroupId
     * @param mixed $newCountry
     * @param mixed $postCodes
     * @return \PHPUnit_Framework_MockObject_MockObject|RuleToUpdate
     */
    private function createMockRuleToUpdate($oldGroupId, $oldCountry, $newGroupId, $newCountry, $postCodes)
    {
        $stubRuleToUpdate = $this->getMock(RuleToUpdate::class, [], [], '', false);
        $stubRuleToUpdate->expects($this->any())->method('getOldCustomerGroupId')->willReturn($oldGroupId);
        $stubRuleToUpdate->expects($this->any())->method('getOldCountry')->willReturn($oldCountry);
        $stubRuleToUpdate->expects($this->any())->method('getNewCustomerGroupId')->willReturn($newGroupId);
        $stubRuleToUpdate->expects($this->any())->method('getNewCountry')->willReturn($newCountry);
        $stubRuleToUpdate->expects($this->any())->method('getNewPostCodes')->willReturn($postCodes);
        return $stubRuleToUpdate;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RuleQuery
     */
    private function createMockRuleQuery()
    {
        $mockRuleQuery = $this->getMock(RuleQuery::class, [], [], '', false);
        $mockRuleQuery->expects($this->any())->method('getCustomerGroupIdValue')->willReturn($this->testGroupId);
        $mockRuleQuery->expects($this->any())->method('getCountryValue')->willReturn($this->testCountry);
        return $mockRuleQuery;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|RuleToDelete
     */
    private function createMockRuleToDelete()
    {
        $mockRuleToDelete = $this->getMock(RuleToDelete::class, [], [], '', false);
        $mockRuleToDelete->expects($this->any())->method('getCustomerGroupIdValue')->willReturn($this->testGroupId);
        $mockRuleToDelete->expects($this->any())->method('getCountryValue')->willReturn($this->testCountry);
        return $mockRuleToDelete;
    }
}
