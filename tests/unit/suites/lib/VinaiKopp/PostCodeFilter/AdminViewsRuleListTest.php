<?php

namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\Rule\Rule;
use VinaiKopp\PostCodeFilter\Storage\ReadModel\RuleReader;

/**
 * @covers \VinaiKopp\PostCodeFilter\AdminViewsRuleList
 */
class AdminViewsRuleListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RuleReader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRuleReader;

    /**
     * @var AdminViewsRuleList
     */
    private $useCase;

    private function assertRuleGetterValuesMatch($getterMethod, array $expectedFieldValues)
    {
        $valuesFromRules = array_values(array_map(function (Rule $rule) use ($getterMethod) {
            return call_user_func([$rule, $getterMethod]);
        }, $this->useCase->fetchRules()));
        $this->assertEquals($expectedFieldValues, $valuesFromRules);
    }

    private function getStubRulesWithCounties(array $countryStrings)
    {
        return $this->getStubRulesWithGetterReturnValues('getCountryValue', $countryStrings);
    }

    private function getStubRulesWithCustomerGroupIds(array $customerGroupIds)
    {
        return $this->getStubRulesWithGetterReturnValues('getCustomerGroupIdValues', $customerGroupIds);
    }

    private function getStubRulesWithPostCodes(array $postCodes)
    {
        return $this->getStubRulesWithGetterReturnValues('getPostCodeValues', $postCodes);
    }

    /**
     * @param string $getterMethod
     * @param mixed[] $values
     * @return Rule[]|\PHPUnit_Framework_MockObject_MockObject[]
     */
    private function getStubRulesWithGetterReturnValues($getterMethod, array $values)
    {
        return array_map(function ($value) use ($getterMethod) {
            $stubRule = $this->getMock(Rule::class);
            $stubRule->method($getterMethod)->willReturn($value);
            return $stubRule;
        }, $values);
    }

    public function setUp()
    {
        $this->mockRuleReader = $this->getMock(RuleReader::class);
        $this->useCase = new AdminViewsRuleList($this->mockRuleReader);
    }

    /**
     * @test
     */
    public function itShouldFetchAllRulesFromTheReader()
    {
        $expected = [$this->getMock(Rule::class)];
        $this->mockRuleReader->expects($this->once())->method('findAll')->willReturn($expected);
        $this->assertSame($expected, $this->useCase->fetchRules());
    }

    /**
     * @test
     * @dataProvider filterMethodDataProvider
     */
    public function itShouldReturnAnEmptyArrayWhenFilteringAnEmptyArray($filterMethod)
    {
        $this->mockRuleReader->method('findAll')->willReturn([]);
        $this->useCase->{$filterMethod}('dummy filter value');
        $this->assertRuleGetterValuesMatch('getCountryValue', []);
    }

    public function filterMethodDataProvider()
    {
        return [
            'country' => ['setCountryFilter'],
            'customer_groups' => ['setCustomerGroupIdFilter'],
            'post_codes' => ['setPostCodeFilter'],
        ];
    }

    /**
     * @test
     * @dataProvider countryFilterProvider
     */
    public function itShouldReturnRulesMatchingCountryFilters($countryFilterValue, $expectedCountries)
    {
        $mockRules = $this->getStubRulesWithCounties(['BB', 'AA', 'CC', 'BC']);
        $this->mockRuleReader->method('findAll')->willReturn($mockRules);
        $this->useCase->setCountryFilter($countryFilterValue);
        $this->assertRuleGetterValuesMatch('getCountryValue', $expectedCountries);
    }

    public function countryFilterProvider()
    {
        return [
            'single-match' => ['CC', ['CC']],
            'partial-match' => ['C', ['CC', 'BC']],
            'no-match' => ['X', []],
            'empty-filter' => ['', ['BB', 'AA', 'CC', 'BC']],
        ];
    }

    /**
     * @test
     * @dataProvider customerGroupIdsFilterProvider
     */
    public function itShouldReturnRulesMatchingACustomerGroupIdFilter($customerGroupIdFilter, $expectedCustomerGroupIds)
    {
        $mockRules = $this->getStubRulesWithCustomerGroupIds([
            [0, 1, 2],
            [0, 1],
            [0],
        ]);
        $this->mockRuleReader->method('findAll')->willReturn($mockRules);
        $this->useCase->setCustomerGroupIdFilter($customerGroupIdFilter);
        $this->assertRuleGetterValuesMatch('getCustomerGroupIdValues', $expectedCustomerGroupIds);
    }

    public function customerGroupIdsFilterProvider()
    {
        return [
            'no-match' => ['3', []],
            'one-match' => ['2', [[0, 1, 2]]],
            'two-matches' => ['1', [[0, 1, 2], [0, 1]]],
            'all-matches' => ['0', [[0, 1, 2], [0, 1], [0]]],
            'empty-filter' => ['', [[0, 1, 2], [0, 1], [0]]]
        ];
    }

    /**
     * @test
     * @dataProvider postCodeFilterProvider
     */
    public function itShouldReturnRulesMatchingAPostCode($postCodeFilter, $expectedPostCodeValues)
    {
        $mockRules = $this->getStubRulesWithPostCodes([
            ['123456', 'ABCD'],
            ['234'],
            []
        ]);
        $this->mockRuleReader->method('findAll')->willReturn($mockRules);
        $this->useCase->setPostCodeFilter($postCodeFilter);
        $this->assertRuleGetterValuesMatch('getPostCodeValues', $expectedPostCodeValues);
    }

    public function postCodeFilterProvider()
    {
        return [
            'no-match' => ['X', []],
            'one-match' => ['BC', [['123456', 'ABCD']]],
            'two-matches' => ['234', [['123456', 'ABCD'], ['234']]],
            'empty-filter' => ['', [['123456', 'ABCD'], ['234'], []]],
        ];
    }

    /**
     * @test
     * @dataProvider sortMethodAndDirectionDataProvider
     */
    public function itShouldSortAnEmptyArray($ascOrDesc, $getterMethod)
    {
        $this->mockRuleReader->method('findAll')->willReturn([]);
        $this->useCase->sortByCountry($ascOrDesc);
        $this->assertRuleGetterValuesMatch($getterMethod, []);
    }

    public function sortMethodAndDirectionDataProvider()
    {
        return [
            'country asc' => ['asc', 'sortByCountry'],
            'country desc' => ['desc', 'sortByCountry'],
            'customer_groups asc' => ['asc', 'sortByCustomerGroupId'],
            'customer_groups desc' => ['desc', 'sortByCustomerGroupId'],
            'post_codes asc' => ['asc', 'sortByPostCode'],
            'post_codes desc' => ['desc', 'sortByPostCode'],
        ];

    }

    /**
     * @test
     * @dataProvider sortByCountryProvider
     */
    public function itShouldSortByCountry($direction, array $expectedCountries)
    {
        $mockRules = $this->getStubRulesWithCounties(['BB', 'CC', 'AA', 'BB']);
        $this->mockRuleReader->method('findAll')->willReturn($mockRules);
        $this->useCase->sortByCountry($direction);
        $this->assertRuleGetterValuesMatch('getCountryValue', $expectedCountries);
    }

    public function sortByCountryProvider()
    {
        $ascending = ['AA', 'BB', 'BB', 'CC'];
        return [
            'asc' => ['asc', $ascending],
            'ASC' => ['ASC', $ascending],
            'desc' => ['desc', array_reverse($ascending)],
            'DESC' => ['DESC', array_reverse($ascending)],
        ];
    }

    /**
     * @test
     * @dataProvider sortByCustomerGroupIdProvider
     */
    public function itShouldSortByCustomerGroupId($direction, $expectedCustomerGroupIds)
    {
        $mockRules = $this->getStubRulesWithCustomerGroupIds([
            [1, 2],
            [1, 2],
            [0, 1, 2],
            [0, 1],
            [2],
            [1, 3]
        ]);
        $this->mockRuleReader->method('findAll')->willReturn($mockRules);
        $this->useCase->sortByCustomerGroupId($direction);
        $this->assertRuleGetterValuesMatch('getCustomerGroupIdValues', $expectedCustomerGroupIds);
    }

    public function sortByCustomerGroupIdProvider()
    {
        $ascending = [[0, 1], [0, 1, 2], [1, 2], [1, 2], [1, 3], [2]];
        return [
            'asc' => ['asc', $ascending],
            'ASC' => ['ASC', $ascending],
            'desc' => ['desc', array_reverse($ascending)],
            'DESC' => ['DESC', array_reverse($ascending)],
        ];
    }

    /**
     * @test
     * @dataProvider sortByPostCodeProvider
     */
    public function itShouldSortByPostCode($direction, $expectedPostCodes)
    {
        $mockRules = $this->getStubRulesWithPostCodes([
            ['BBB', 'CCC'],
            ['BBB', 'CCC'],
            ['AAA', 'BBB', 'CCC'],
            ['CCCC'],
            ['AAA', 'BBB'],
            ['CCC'],
            ['BBB', 'DDD'],
            ['CCD'],
        ]);
        $this->mockRuleReader->method('findAll')->willReturn($mockRules);
        $this->useCase->sortByPostCode($direction);
        $this->assertRuleGetterValuesMatch('getPostCodeValues', $expectedPostCodes);
    }

    public function sortByPostCodeProvider()
    {
        $ascending = [
            ['AAA', 'BBB'],
            ['AAA', 'BBB', 'CCC'],
            ['BBB', 'CCC'],
            ['BBB', 'CCC'],
            ['BBB', 'DDD'],
            ['CCC'],
            ['CCCC'],
            ['CCD'],
        ];
        return [
            'asc' => ['asc', $ascending],
            'ASC' => ['ASC', $ascending],
            'desc' => ['desc', array_reverse($ascending)],
            'DESC' => ['DESC', array_reverse($ascending)],
        ];
    }
}
