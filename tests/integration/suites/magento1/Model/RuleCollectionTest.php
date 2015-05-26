<?php

namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\Rule\Rule;
use VinaiKopp\PostCodeFilter\AdminViewsRuleList;

/**
 * @covers \VinaiKopp_PostCodeFilter_Model_RuleCollection
 */
class RuleCollectionTest extends Mage1IntegrationTestCase
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
    public function itShouldAddAnItemForEachRuleReturnedByTheStorage()
    {
        $this->mockUseCase->expects($this->atLeastOnce())->method('fetchRules')->willReturn(
            [$this->getMock(Rule::class)]
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
        $this->mockUseCase->method('fetchRules')->willReturn([]);
        $this->collection->load();
        $this->assertTrue($this->collection->isLoaded());
    }

    /**
     * @test
     */
    public function itShouldReturnTheNumberOfItemsLoaded()
    {
        $this->mockUseCase->method('fetchRules')->willReturn([
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
        $this->mockUseCase->method('fetchRules')->willReturn([
            $this->getMockRuleWithCountry('BB'),
            $this->getMockRuleWithCountry('AA'),
            $this->getMockRuleWithCountry('CC'),
        ]);
        $this->collection->addFieldToFilter('country', ['unknown-filter-type' => 'some-filter-value']);
        $this->collection->load();
    }

    /**
     * @test
     */
    public function itShouldSetCountryFiltersOnTheUseCase()
    {
        $filterValue = 'filter value';
        $this->mockUseCase->method('fetchRules')->willReturn([]);
        $this->mockUseCase->expects($this->once())->method('setCountryFilter')->with($filterValue);

        $filterBlock = new \Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Text();
        $filterBlock->setData('value', $filterValue);
        $this->collection->addFieldToFilter('country', $filterBlock->getCondition());
        $this->collection->load();
    }

    /**
     * @test
     */
    public function itShouldSetCustomerGroupIdFiltersOnTheUseCase()
    {
        $filterValue = '3';
        $this->mockUseCase->method('fetchRules')->willReturn([]);
        $this->mockUseCase->expects($this->once())->method('setCustomerGroupIdFilter')->with($filterValue);

        $filterBlock = new \Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select();
        $filterBlock->setData('value', $filterValue);
        $this->collection->addFieldToFilter('customer_groups', $filterBlock->getCondition());
        $this->collection->load();
    }

    /**
     * @test
     */
    public function itShouldSetPostCodeFiltersOnTheUseCase()
    {
        $filterValue = 'ABC';
        $this->mockUseCase->method('fetchRules')->willReturn([]);
        $this->mockUseCase->expects($this->once())->method('setPostCodeFilter')->with($filterValue);

        $filterBlock = new \Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Text();
        $filterBlock->setData('value', $filterValue);
        $this->collection->addFieldToFilter('post_codes', $filterBlock->getCondition());
        $this->collection->load();
    }

    /**
     * @test
     * @dataProvider sortDirectionDataProvider
     */
    public function itShouldCallTheSortByCountryMethodOnTheUseCase($sortMethod, $collectionField, $direction)
    {
        $this->mockUseCase->method('fetchRules')->willReturn([]);
        $this->mockUseCase->expects($this->once())->method($sortMethod)->with($direction);
        $this->collection->setOrder($collectionField, $direction);
    }

    /**
     * @test
     */
    public function addOrderShouldDelegateToSetOrder()
    {
        if (!class_exists(RuleCollectionSpy::class)) {
            require 'RuleCollectionSpy.php';
        }
        $collection = new RuleCollectionSpy();

        $this->assertFalse($collection->setOrderWasCalled);
        $collection->addOrder('country');
        $this->assertTrue($collection->setOrderWasCalled);
    }

    public function sortDirectionDataProvider()
    {
        return [
            'country asc' => ['sortByCountry', 'country', 'asc'],
            'country desc' => ['sortByCountry', 'country', 'desc'],
            'customer_groups asc' => ['sortByCustomerGroupId', 'customer_groups', 'asc'],
            'customer_groups desc' => ['sortByCustomerGroupId', 'customer_groups', 'desc'],
            'post_codes asc' => ['sortByPostCode', 'post_codes', 'asc'],
            'post_codes desc' => ['sortByPostCode', 'post_codes', 'desc'],
        ];
    }
}
