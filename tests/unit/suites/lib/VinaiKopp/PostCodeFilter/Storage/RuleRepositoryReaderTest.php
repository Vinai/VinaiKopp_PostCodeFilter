<?php

namespace VinaiKopp\PostCodeFilter\Storage;

use VinaiKopp\PostCodeFilter\Rule\Components\Country;
use VinaiKopp\PostCodeFilter\Rule\ExistingRule;
use VinaiKopp\PostCodeFilter\Rule\NonexistentRule;
use VinaiKopp\PostCodeFilter\Rule\Rule;
use VinaiKopp\PostCodeFilter\Storage\ReadModel\RuleReader;
use VinaiKopp\PostCodeFilter\Storage\ReadModel\RuleSpecByCountryAndGroupId;
use VinaiKopp\PostCodeFilter\Storage\ReadModel\RuleSpecByCountryAndGroupIds;

/**
 * @covers \VinaiKopp\PostCodeFilter\Storage\RuleRepositoryReader
 * @uses   \VinaiKopp\PostCodeFilter\Rule\NonexistentRule
 * @uses   \VinaiKopp\PostCodeFilter\Rule\ExistingRule
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupId
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupIdList
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\Country
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\PostCodeList
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\PostCode
 */
class RuleRepositoryReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RuleRepositoryReader
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
     * @return RuleSpecByCountryAndGroupId|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createMockRuleSpecByCountryAndGroupId()
    {
        $mockRuleSpec = $this->getMock(RuleSpecByCountryAndGroupId::class, [], [], '', false);
        $mockRuleSpec->method('getCustomerGroupId')->willReturn(
            $this->getMock(CustomerGroupId::class, [], [], '', false)
        );
        $mockRuleSpec->method('getCountry')->willReturn(
            $this->getMock(Country::class, [], [], '', false)
        );
        $mockRuleSpec->method('getCustomerGroupIdValue')->willReturn($this->testGroupId);
        $mockRuleSpec->method('getCountryValue')->willReturn($this->testCountry);
        return $mockRuleSpec;
    }

    /**
     * @return RuleSpecByCountryAndGroupIds|\PHPUnit_Framework_MockObject_MockObject
     */
    private function createMockRuleSpecByCountryAndGroupIdsFor($country, array $groupIds)
    {
        $mockRuleSpec = $this->getMock(RuleSpecByCountryAndGroupIds::class, [], [], '', false);
        $mockRuleSpec->method('getCountry')->willReturn(
            $this->getMock(Country::class, [], [], '', false)
        );
        $mockRuleSpec->method('getCountryValue')->willReturn($country);
        $mockRuleSpec->method('getCustomerGroupIds')->willReturn(
            [$this->getMock(CustomerGroupId::class, [], [], '', false)]
        );
        $mockRuleSpec->method('getCustomerGroupIdValues')->willReturn($groupIds);
        return $mockRuleSpec;
    }

    protected function setUp()
    {
        $this->mockStorage = $this->getMock(RuleStorage::class);
        $this->repository = new RuleRepositoryReader($this->mockStorage);
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
    public function itShouldReturnARuleResultFromASpec()
    {
        $result = $this->repository->findByCountryAndGroupId($this->createMockRuleSpecByCountryAndGroupId());
        $this->assertInstanceOf(Rule::class, $result);
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
        $result = $this->repository->findByCountryAndGroupId($this->createMockRuleSpecByCountryAndGroupId());
        $this->assertInstanceOf(NonexistentRule::class, $result);
    }

    public function storageFoundNoMatchesReturnValueProvider()
    {
        return [
            'empty array' => [[]],
            'null' => [null]
        ];
    }

    /**
     * @test
     */
    public function itShouldReturnAFoundRuleIfTheStorageReturnsAMatch()
    {
        $this->mockStorage->expects($this->once())->method('findPostCodesByCountryAndGroupId')
            ->with($this->testCountry, $this->testGroupId)
            ->willReturn(['123']);
        $result = $this->repository->findByCountryAndGroupId($this->createMockRuleSpecByCountryAndGroupId());
        $this->assertInstanceOf(ExistingRule::class, $result);
    }

    /**
     * @test
     */
    public function itShouldReturnAllMatchesForGivenCountryAndGroupIds()
    {
        $specGroupIds = [3, 4, 5, 6];
        $this->mockStorage->expects($this->once())->method('findRulesByCountryAndGroupIds')
            ->with($this->testCountry, $specGroupIds)
            ->willReturn([
                ['customer_group_id' => 3, 'country' => 'DE', 'post_codes' => [123, 456]],
                ['customer_group_id' => 4, 'country' => 'DE', 'post_codes' => [123, 456]],
                ['customer_group_id' => 6, 'country' => 'DE', 'post_codes' => [123, 456]],
            ]);
        $ruleResult = $this->repository->findByCountryAndGroupIds(
            $this->createMockRuleSpecByCountryAndGroupIdsFor('DE', $specGroupIds)
        );
        $this->assertInstanceOf(ExistingRule::class, $ruleResult);
        $this->assertSame([3, 4, 6], $ruleResult->getCustomerGroupIdValues());
        $this->assertSame('DE', $ruleResult->getCountryValue());
        $this->assertSame([123, 456], $ruleResult->getPostCodeValues());
    }

    /**
     * @test
     */
    public function itShouldReturnRuleNotFoundIfThereAreNoMatchesForGivenCountryAndGroupIds()
    {
        $this->mockStorage->expects($this->once())->method('findRulesByCountryAndGroupIds')
            ->with($this->testCountry, [2, 3])
            ->willReturn([]);
        $ruleResult = $this->repository->findByCountryAndGroupIds(
            $this->createMockRuleSpecByCountryAndGroupIdsFor('DE', [2, 3])
        );
        $this->assertInstanceOf(NonexistentRule::class, $ruleResult);
    }

    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exceptions\NonMatchingRecordInResultException
     * @expectedExceptionMessage The country "XX" does not match the query country value "DE"
     */
    public function itShouldThrowIfTheStorageReturnsACountryNotMatchingTheSpec()
    {
        $this->mockStorage->expects($this->once())->method('findRulesByCountryAndGroupIds')
            ->willReturn([
                ['customer_group_id' => 12, 'country' => 'XX', 'post_codes' => [123, 456]]
            ]);
        $this->repository->findByCountryAndGroupIds(
            $this->createMockRuleSpecByCountryAndGroupIdsFor('DE', [12])
        );
    }

    /**
     * @test
     * @expectedException \VinaiKopp\PostCodeFilter\Exceptions\NonMatchingRecordInResultException
     * @expectedExceptionMessage The customer group ID "12" does not match the query customer group ID values "13, 14"
     */
    public function itShouldThrowIfTheStorageReturnsACustomerGroupIdNotMatchingTheSpec()
    {
        $this->mockStorage->expects($this->once())->method('findRulesByCountryAndGroupIds')
            ->willReturn([
                ['customer_group_id' => 12, 'country' => 'DE', 'post_codes' => [123, 456]]
            ]);
        $this->repository->findByCountryAndGroupIds(
            $this->createMockRuleSpecByCountryAndGroupIdsFor('DE', [13, 14])
        );
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
        $this->assertContainsOnly(ExistingRule::class, $result);
        
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
}
