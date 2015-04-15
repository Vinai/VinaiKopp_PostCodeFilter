<?php

namespace VinaiKopp\PostCodeFilter\RuleComponents;

/**
 * @covers \VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId
 */
class CustomerGroupIdListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function itShouldBeConstructableWithIntegers()
    {
        $list = CustomerGroupIdList::fromArray([5]);
        $this->assertInstanceOf(CustomerGroupIdList::class, $list);
    }

    /**
     * @test
     */
    public function itShouldBeConstructableWithCustomerGroupIdInstances()
    {
        $lst = CustomerGroupIdList::fromArray([CustomerGroupId::fromInt(5)]);
        $this->assertInstanceOf(CustomerGroupIdList::class, $lst);
    }

    /**
     * @test
     */
    public function itShouldReturnTheCustomerGroupIdInstances()
    {
        $list = CustomerGroupIdList::fromArray([5]);
        $result = $list->getCustomerGroupIds();
        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $this->assertContainsOnly(CustomerGroupId::class, $result);
    }

    /**
     * @test
     */
    public function itShouldReturnTheCustomerGroupIdValues()
    {
        $list = CustomerGroupIdList::fromArray([5]);
        $this->assertSame([5], $list->getValues());
    }

    /**
     * @test
     */
    public function itShouldFilterOutDuplicates()
    {
        $list = CustomerGroupIdList::fromArray([1, 2, 1, 2, 3]);
        $this->assertSame([1, 2, 3], $list->getValues());
    }
}
