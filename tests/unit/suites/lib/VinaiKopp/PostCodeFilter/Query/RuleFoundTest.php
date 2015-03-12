<?php


namespace VinaiKopp\PostCodeFilter\Query;

use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\RuleComponents\PostCode;
use VinaiKopp\PostCodeFilter\RuleComponents\PostCodeList;

/**
 * @covers \VinaiKopp\PostCodeFilter\Query\RuleFound
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\Country
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\PostCodeList
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\PostCode
 */
class RuleFoundTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RuleFound
     */
    private $rule;
    
    private $matchingPostCode = 1234;
    
    private $customerGroupId = 88;
    
    private $country = 'PP';

    protected function setUp()
    {
        $this->rule = new RuleFound(
            CustomerGroupIdList::fromArray([$this->customerGroupId]),
            Country::fromCode($this->country),
            PostCodeList::fromArray([$this->matchingPostCode])
        );
    }

    /**
     * @test
     */
    public function itShouldBeARuleResult()
    {
        $this->assertInstanceOf(RuleResult::class, $this->rule);
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
