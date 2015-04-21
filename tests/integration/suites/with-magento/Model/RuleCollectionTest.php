<?php

namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\Query\Rule;
use VinaiKopp\PostCodeFilter\UseCases\AdminViewsRuleList;

/**
 * @covers \VinaiKopp_PostCodeFilter_Model_RuleCollection
 */
class RuleCollectionTest extends IntegrationTestCase
{
    /**
     * @var \VinaiKopp_PostCodeFilter_Model_RuleCollection
     */
    private $collection;

    /**
     * @var AdminViewsRuleList|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockUseCase;

    /**
     * @return Rule|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getStubRule()
    {
        $stubRule = $this->getMock(Rule::class);
        return $stubRule;
    }

    /**
     * @param string $iso2country
     * @return \PHPUnit_Framework_MockObject_MockObject|Rule
     */
    private function getMockRuleWithCountry($iso2country)
    {
        $stubRule = $this->getMock(Rule::class, [], [], '', false);
        $stubRule->method('getCountryValue')->willReturn($iso2country);
        return $stubRule;
    }

    /**
     * @param int[] $customerGroupIds
     * @return \PHPUnit_Framework_MockObject_MockObject|Rule
     */
    private function getMockRuleWithCustomerGroupIds(array $customerGroupIds)
    {
        $stubRule = $this->getMock(Rule::class, [], [], '', false);
        $stubRule->method('getCustomerGroupIdValues')->willReturn($customerGroupIds);
        return $stubRule;
    }

    /**
     * @param string[] $postCodes
     * @return \PHPUnit_Framework_MockObject_MockObject|Rule
     */
    private function getMockRuleWithPostCodes(array $postCodes)
    {
        $stubRule = $this->getMock(Rule::class, [], [], '', false);
        $stubRule->method('getPostCodeValues')->willReturn($postCodes);
        return $stubRule;
    }

    /**
     * @param string $fieldName
     * @param string[] $expectedItemFieldValues
     */
    private function assertCollectionItemsFieldsMatch($fieldName, array $expectedItemFieldValues)
    {
        $zeroBasedItemArray = array_values($this->collection->getItems());
        $valuesInCollectionItems = array_map(function (\Varien_Object $item) use ($fieldName) {
            return $item->getData($fieldName);
        }, $zeroBasedItemArray);
        $this->assertEquals($expectedItemFieldValues, $valuesInCollectionItems);
    }

    protected function setUp()
    {
        $this->mockUseCase = $this->getMock(AdminViewsRuleList::class, [], [], '', false);
        $this->collection = new \VinaiKopp_PostCodeFilter_Model_RuleCollection();
        $this->collection->setUseCase($this->mockUseCase);
    }

    /**
     * @test
     */
    public function itShouldBeAMagentoCollection()
    {
        $this->assertInstanceOf(\Varien_Data_Collection_Db::class, $this->collection);
    }

    /**
     * @test
     */
    public function itShouldStartEmptyAndUnloaded()
    {
        $this->assertTrue(false == $this->collection->isLoaded());
        $this->assertAttributeCount(0, '_items', $this->collection);
    }

    /**
     * @test
     */
    public function itShouldAddAnItemsIfStorageReturnsARule()
    {
        $this->mockUseCase->expects($this->atLeastOnce())->method('fetchAllRules')->willReturn(
            [$this->getStubRule()]
        );
        $this->collection->load();
        $this->assertCount(1, $this->collection);
        $this->assertAttributeContainsOnly(\Varien_Object::class, '_items', $this->collection);
    }

    /**
     * @test
     */
    public function itShouldBeLoadedAfterLoadWasCalled()
    {
        $this->mockUseCase->method('fetchAllRules')->willReturn([]);
        $this->collection->load();
        $this->assertTrue($this->collection->isLoaded());
    }

    /**
     * @test
     */
    public function itShouldReturnTheNumberOfItemsLoaded()
    {
        $this->mockUseCase->method('fetchAllRules')->willReturn([
            $this->getMockRuleWithCountry('XX')
        ]);
        $this->assertSame(1, $this->collection->getSize());
    }

    /**
     * @test
     * @expectedException \Mage_Core_Exception
     */
    public function itShouldThrowAnExceptionForUnsupportedFilterOperators()
    {
        $this->mockUseCase->method('fetchAllRules')->willReturn([
            $this->getMockRuleWithCountry('BB'),
            $this->getMockRuleWithCountry('AA'),
            $this->getMockRuleWithCountry('CC'),
        ]);
        $this->collection->addFieldToFilter('country', ['unknown-filter-type' => 'some-filter-value']);
        $this->collection->load();
    }

    /**
     * @test
     * @dataProvider countryFilterDataProvider
     * @param string $filterValue
     * @param string[] $expectedItemCountries
     */
    public function itShouldReturnRulesMatchingCountryFilters($filterValue, $expectedItemCountries)
    {
        $this->mockUseCase->method('fetchAllRules')->willReturn([
            $this->getMockRuleWithCountry('BB'),
            $this->getMockRuleWithCountry('AA'),
            $this->getMockRuleWithCountry('CC'),
        ]);
        $filterBlock = new \Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Text();
        $filterBlock->setData('value', $filterValue);
        $this->collection->addFieldToFilter('country', $filterBlock->getCondition());
        $this->collection->load();
        $this->assertCollectionItemsFieldsMatch('country', $expectedItemCountries);
    }

    public function countryFilterDataProvider()
    {
        return [
            'match-partial' => ['A', ['AA']],
            'match-exact' => ['AA', ['AA']],
            'no-match' => ['foo', []],
            'empty-filter' => ['', ['BB', 'AA', 'CC']],
        ];
    }

    /**
     * @test
     * @dataProvider customerGroupIdsFilterDataProvider
     * @param string $filterValue
     * @param array[] $expectedItemGroupIds
     */
    public function itShouldMatchIfAnItemsCustomerGroupMatchesTheFilter($filterValue, array $expectedItemGroupIds)
    {
        $this->mockUseCase->method('fetchAllRules')->willReturn([
            $this->getMockRuleWithCustomerGroupIds([0, 1, 2]),
            $this->getMockRuleWithCustomerGroupIds([0, 1]),
            $this->getMockRuleWithCustomerGroupIds([0]),
        ]);
        $filterBlock = new \Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select();
        $filterBlock->setData('value', $filterValue);
        $this->collection->addFieldToFilter('customer_groups', $filterBlock->getCondition());
        $this->collection->load();
        $this->assertCollectionItemsFieldsMatch('customer_groups', $expectedItemGroupIds);
    }

    public function customerGroupIdsFilterDataProvider()
    {
        return [
            'empty-filter' => ['', [[0, 1, 2], [0, 1], [0]]],
            'matches-all' => ['0', [[0, 1, 2], [0, 1], [0]]],
            'matches-some' => ['1', [[0, 1, 2], [0, 1]]],
            'matches-one' => ['2', [[0, 1, 2]]],
            'no-match' => ['3', []],
        ];
    }

    /**
     * @test
     * @dataProvider sortByCountryDataProvider
     * @param string $direction
     * @param string[] $expectedItemCountries
     */
    public function itShouldSortByCountry($direction, $expectedItemCountries)
    {
        $this->mockUseCase->method('fetchAllRules')->willReturn([
            $this->getMockRuleWithCountry('BB'),
            $this->getMockRuleWithCountry('CC'),
            $this->getMockRuleWithCountry('AA'),
            $this->getMockRuleWithCountry('BB'),
        ]);
        $this->collection->setOrder('country', $direction);
        $this->assertCollectionItemsFieldsMatch('country', $expectedItemCountries);
    }

    public function sortByCountryDataProvider()
    {
        return [
            'asc' => ['asc', ['AA', 'BB', 'BB', 'CC']],
            'desc' => ['desc', ['CC', 'BB', 'BB', 'AA']],
        ];
    }

    /**
     * @test
     * @dataProvider sortByCustomerGroupDataProvider
     */
    public function itShouldSortByCustomerGroupId($direction, $expectedItemGroupIds)
    {
        $this->mockUseCase->method('fetchAllRules')->willReturn([
            $this->getMockRuleWithCustomerGroupIds([1, 2]),
            $this->getMockRuleWithCustomerGroupIds([1, 3]),
            $this->getMockRuleWithCustomerGroupIds([0, 1, 2]),
            $this->getMockRuleWithCustomerGroupIds([1, 3]),
            $this->getMockRuleWithCustomerGroupIds([2]),
            $this->getMockRuleWithCustomerGroupIds([2, 3]),
        ]);
        $this->collection->setOrder('customer_groups', $direction);
        $this->assertCollectionItemsFieldsMatch('customer_groups', $expectedItemGroupIds);
    }

    public function sortByCustomerGroupDataProvider()
    {
        return [
            'asc' => ['asc', [[0, 1, 2], [1, 2], [1, 3], [1, 3], [2], [2, 3]]],
            'desc' => ['desc', [[2, 3], [2], [1, 3], [1, 3], [1, 2], [0, 1, 2]]]
        ];
    }
}
