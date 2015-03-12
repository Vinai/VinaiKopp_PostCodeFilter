<?php


namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\Command\RuleToAdd;
use VinaiKopp\PostCodeFilter\Command\RuleToDelete;
use VinaiKopp\PostCodeFilter\Command\RuleWriter;
use VinaiKopp\PostCodeFilter\Query\RuleFound;
use VinaiKopp\PostCodeFilter\Query\RuleNotFound;
use VinaiKopp\PostCodeFilter\Query\QueryByCountryAndGroupId;
use VinaiKopp\PostCodeFilter\Query\QueryByCountryAndGroupIds;
use VinaiKopp\PostCodeFilter\Query\RuleReader;
use VinaiKopp\PostCodeFilter\Query\RuleResult;
use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId;

/**
 * @covers \VinaiKopp\PostCodeFilter\RuleRepository
 * @uses   \VinaiKopp\PostCodeFilter\Query\RuleNotFound
 * @uses   \VinaiKopp\PostCodeFilter\Query\RuleFound
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\Country
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\PostCodeList
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\PostCode
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
    public function itShouldImplementRuleWriter()
    {
        $this->assertInstanceOf(RuleWriter::class, $this->repository);
    }

    /**
     * @test
     */
    public function itShouldImplementRuleReader()
    {
        $this->assertInstanceOf(RuleReader::class, $this->repository);
    }

    /**
     * @test
     */
    public function itShouldReturnARuleResultFromAQuery()
    {
        $result = $this->repository->findByCountryAndGroupId($this->createMockRuleQueryByCountryAndGroupId());
        $this->assertInstanceOf(RuleResult::class, $result);
    }

    /**
     * @test
     * @dataProvider storageFoundNoMatchesReturnValueProvider
     */
    public function itShouldReturnARuleNotFoundIfTheStorageReturnsNoMatches($storageFoundNoMatchesReturnValue)
    {
        $this->mockStorage->expects($this->once())->method('findPostCodesByCountryAndGroupId')
            ->with($this->testCountry, $this->testGroupId)
            ->willReturn($storageFoundNoMatchesReturnValue);
        $result = $this->repository->findByCountryAndGroupId($this->createMockRuleQueryByCountryAndGroupId());
        $this->assertInstanceOf(RuleNotFound::class, $result);
    }

    public function storageFoundNoMatchesReturnValueProvider()
    {
        return ['empty array' => [[]], 'null' => [null]];
    }

    /**
     * @test
     */
    public function itShouldReturnAFoundRuleIfTheStorageReturnsAMatch()
    {
        $this->mockStorage->expects($this->once())->method('findPostCodesByCountryAndGroupId')
            ->with($this->testCountry, $this->testGroupId)
            ->willReturn(['123']);
        $result = $this->repository->findByCountryAndGroupId($this->createMockRuleQueryByCountryAndGroupId());
        $this->assertInstanceOf(RuleFound::class, $result);
    }

    /**
     * @test
     */
    public function itShouldReturnAllMatchesForGivenCountryAndIds()
    {
        $queryGroupIds = [3, 4, 5, 6];
        $this->mockStorage->expects($this->once())->method('findRulesByCountryAndGroupIds')
            ->with($this->testCountry, $queryGroupIds)
            ->willReturn([
                ['customer_group_id' => 3, 'country' => 'DE', 'post_codes' => [123, 456]],
                ['customer_group_id' => 4, 'country' => 'DE', 'post_codes' => [123, 456]],
                ['customer_group_id' => 6, 'country' => 'DE', 'post_codes' => [123, 456]],
            ]);
        $ruleFound = $this->repository->findByCountryAndGroupIds(
            $this->createMockRuleQueryByCountryAndGroupIdsFor('DE', $queryGroupIds)
        );
        $this->assertInstanceOf(RuleFound::class, $ruleFound);
        $this->assertSame([3, 4, 6], $ruleFound->getCustomerGroupIdValues());
        $this->assertSame('DE', $ruleFound->getCountryValue());
        $this->assertSame([123, 456], $ruleFound->getPostCodeValues());
    }

    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exceptions\NonMatchingRecordInResultException
     * @expectedExceptionMessage The country "XX" does not match the query country value "DE"
     */
    public function itShouldThrowIfTheStorageReturnsACountryNotMatchingTheQuery()
    {
        $this->mockStorage->expects($this->once())->method('findRulesByCountryAndGroupIds')
            ->willReturn([
                ['customer_group_id' => 12, 'country' => 'XX', 'post_codes' => [123, 456]]
            ]);
        $this->repository->findByCountryAndGroupIds(
            $this->createMockRuleQueryByCountryAndGroupIdsFor('DE', [12])
        );
    }

    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exceptions\NonMatchingRecordInResultException
     * @expectedExceptionMessage The customer group ID "12" does not match the query customer group ID values "13, 14"
     */
    public function itShouldThrowIfTheStorageReturnsACustomerGroupIdNotMatchingTheQuery()
    {
        $this->mockStorage->expects($this->once())->method('findRulesByCountryAndGroupIds')
            ->willReturn([
                ['customer_group_id' => 12, 'country' => 'DE', 'post_codes' => [123, 456]]
            ]);
        $this->repository->findByCountryAndGroupIds(
            $this->createMockRuleQueryByCountryAndGroupIdsFor('DE', [13, 14])
        );
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
    public function itShouldDelegateDeletesToTheStorage()
    {
        $stubRuleToDelete = $this->createMockRuleToDelete();
        $this->mockStorage->expects($this->once())->method('delete')->with($stubRuleToDelete);
        $this->repository->deleteRule($stubRuleToDelete);
    }

    /**
     * @test
     */
    public function itShouldReturnAnArrayWithAllCombinedRules()
    {
        $this->mockStorage->expects($this->once())->method('findAllRules')
            ->willReturn([
                ['customer_group_id' => 12, 'country' => 'LE', 'post_codes' => [123, 456]],
                ['customer_group_id' => 13, 'country' => 'LE', 'post_codes' => [123, 456]],
                
                ['customer_group_id' => 13, 'country' => 'GB', 'post_codes' => [123, 456, 777]],
                ['customer_group_id' => 15, 'country' => 'GB', 'post_codes' => [123, 456, 777]],
                
                ['customer_group_id' => 10, 'country' => 'GB', 'post_codes' => [123]],
            ]);
        
        $result = $this->repository->findAll();
        
        $this->assertInternalType('array', $result);
        $this->assertCount(3, $result);
        $this->assertContainsOnly(RuleFound::class, $result);
        
        $this->assertSame([12, 13], $result[0]->getCustomerGroupIdValues());
        $this->assertSame([13, 15], $result[1]->getCustomerGroupIdValues());
        $this->assertSame([10], $result[2]->getCustomerGroupIdValues());
        
        $this->assertSame('LE', $result[0]->getCountryValue());
        $this->assertSame('GB', $result[1]->getCountryValue());
        $this->assertSame('GB', $result[2]->getCountryValue());
        
        $this->assertSame([123, 456], $result[0]->getPostCodeValues());
        $this->assertSame([123, 456, 777], $result[1]->getPostCodeValues());
        $this->assertSame([123], $result[2]->getPostCodeValues());
    }

    /**
     * @param mixed $groupId
     * @param mixed $country
     * @param mixed $postCodes
     * @return RuleToAdd|\PHPUnit_Framework_MockObject_MockObject
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
     * @return QueryByCountryAndGroupId|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createMockRuleQueryByCountryAndGroupId()
    {
        $mockRuleQuery = $this->getMock(QueryByCountryAndGroupId::class, [], [], '', false);
        $mockRuleQuery->expects($this->any())->method('getCustomerGroupId')->willReturn(
            $this->getMock(CustomerGroupId::class, [], [], '', false)
        );
        $mockRuleQuery->expects($this->any())->method('getCountry')->willReturn(
            $this->getMock(Country::class, [], [], '', false)
        );
        $mockRuleQuery->expects($this->any())->method('getCustomerGroupIdValue')->willReturn($this->testGroupId);
        $mockRuleQuery->expects($this->any())->method('getCountryValue')->willReturn($this->testCountry);
        return $mockRuleQuery;
    }

    /**
     * @return QueryByCountryAndGroupIds|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createMockRuleQueryByCountryAndGroupIdsFor($country, array $groupIds)
    {
        $mockRuleQuery = $this->getMock(QueryByCountryAndGroupIds::class, [], [], '', false);
        $mockRuleQuery->expects($this->any())->method('getCountry')->willReturn(
            $this->getMock(Country::class, [], [], '', false)
        );
        $mockRuleQuery->expects($this->any())->method('getCountryValue')->willReturn($country);
        $mockRuleQuery->expects($this->any())->method('getCustomerGroupIds')->willReturn(
            [$this->getMock(CustomerGroupId::class, [], [], '', false)]
        );
        $mockRuleQuery->expects($this->any())->method('getCustomerGroupIdValues')->willReturn($groupIds);
        return $mockRuleQuery;
    }

    /**
     * @return RuleToDelete|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createMockRuleToDelete()
    {
        $mockRuleToDelete = $this->getMock(RuleToDelete::class, [], [], '', false);
        $mockRuleToDelete->expects($this->any())->method('getCustomerGroupIdValue')->willReturn($this->testGroupId);
        $mockRuleToDelete->expects($this->any())->method('getCountryValue')->willReturn($this->testCountry);
        return $mockRuleToDelete;
    }
}
