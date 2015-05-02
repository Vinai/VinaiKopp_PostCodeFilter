<?php

namespace VinaiKopp\PostCodeFilter\UseCases;

use VinaiKopp\PostCodeFilter\ReadModel\RuleReader;
use VinaiKopp\PostCodeFilter\ReadModel\Rule;

/**
 * @covers \VinaiKopp\PostCodeFilter\UseCases\AdminViewsRuleList
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
        $valuesFromRules = array_values(array_map(function(Rule $rule) use ($getterMethod) {
            return call_user_func([$rule, $getterMethod]);
        }, $this->useCase->fetchAllRules()));
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
        return array_map(function($value) use ($getterMethod) {
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
        $this->assertSame($expected, $this->useCase->fetchAllRules());
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
}
