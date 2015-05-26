<?php

namespace VinaiKopp\PostCodeFilter\Rule;

use VinaiKopp\PostCodeFilter\Rule\Components\Country;
use VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\Rule\Components\PostCode;
use VinaiKopp\PostCodeFilter\Rule\Components\PostCodeList;

/**
 * @covers \VinaiKopp\PostCodeFilter\Rule\ExistingRule
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupId
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupIdList
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\Country
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\PostCodeList
 * @uses   \VinaiKopp\PostCodeFilter\Rule\Components\PostCode
 */
class ExistingRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ExistingRule
     */
    private $rule;
    
    private $matchingPostCode = 1234;
    
    private $customerGroupId = 88;
    
    private $country = 'PP';

    protected function setUp()
    {
        $this->rule = new ExistingRule(
            CustomerGroupIdList::fromArray([$this->customerGroupId]),
            Country::fromIso2Code($this->country),
            PostCodeList::fromArray([$this->matchingPostCode])
        );
    }

    /**
     * @test
     */
    public function itShouldBeARuleResult()
    {
        $this->assertInstanceOf(Rule::class, $this->rule);
    }

    /**
     * @test
     */
    public function itShouldReturnFalseIfThePostCodeIsNotFound()
    {
        $this->assertFalse($this->rule->isPostCodeAllowed('dummy'));
    }

    /**
     * @test
     * @dataProvider validPostCodeTypeProvider
     * @param $postCode
     */
    public function itShouldTakeAllValidPostCodeTypes($postCode)
    {
        $this->assertTrue($this->rule->isPostCodeAllowed($postCode));
    }

    public function validPostCodeTypeProvider()
    {
        return [
            'int' => [(int) $this->matchingPostCode],
            'string' => [(string) $this->matchingPostCode],
            'PostCode' => [PostCode::fromIntOrString($this->matchingPostCode)],
        ];
    }

    /**
     * @test
     */
    public function itShouldReturnTheCustomerGroupIdListInstance()
    {
        $result = $this->rule->getCustomerGroupIds();
        $this->assertInstanceOf(CustomerGroupIdList::class, $result);
    }

    /**
     * @test
     */
    public function itShouldReturnTheCustomerGroupIdValue()
    {
        $this->assertEquals([$this->customerGroupId], $this->rule->getCustomerGroupIdValues());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCountryInstance()
    {
        $this->assertInstanceOf(Country::class, $this->rule->getCountry());
    }

    /**
     * @test
     */
    public function itShouldReturnTheCountryValue()
    {
        $this->assertEquals($this->country, $this->rule->getCountryValue());
    }

    /**
     * @test
     */
    public function itShouldReturnThePostCodeList()
    {
        $this->assertInstanceOf(PostCodeList::class, $this->rule->getPostCodes());
    }

    /**
     * @test
     */
    public function itShouldReturnThePostCodeValues()
    {
        $this->assertEquals([$this->matchingPostCode], $this->rule->getPostCodeValues());
    }
}
