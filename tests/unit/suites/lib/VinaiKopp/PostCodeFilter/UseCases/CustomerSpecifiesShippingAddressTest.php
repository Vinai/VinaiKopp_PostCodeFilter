<?php

namespace VinaiKopp\PostCodeFilter\UseCases;

use VinaiKopp\PostCodeFilter\Query\RuleReader;
use VinaiKopp\PostCodeFilter\Query\Rule;

/**
 * @covers \VinaiKopp\PostCodeFilter\UseCases\CustomerPlacesOrder
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\Country
 * @uses   \VinaiKopp\PostCodeFilter\Query\RuleSpecByCountryAndGroupId
 */
class CustomerSpecifiesShippingAddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CustomerSpecifiesShippingAddress
     */
    private $useCase;

    /**
     * @var string
     */
    private $country = 'DE';

    /**
     * @var string
     */
    private $postCode = '69151';

    /**
     * @var int
     */
    private $customerGroupId = 3;

    /**
     * @var RuleReader|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRuleReader;

    /**
     * @var Rule|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRule;

    protected function setUp()
    {
        $this->mockRule = $this->getMock(Rule::class);
        $this->mockRuleReader = $this->getMock(RuleReader::class);
        $this->mockRuleReader->method('findByCountryAndGroupId')->willReturn($this->mockRule);
        $this->useCase = new CustomerSpecifiesShippingAddress($this->mockRuleReader);
    }
    
    /**
     * @test
     */
    public function itShouldReturnFalseIfTheCustomerMayNotOrder()
    {
        $this->mockRule->expects($this->once())->method('isPostCodeAllowed')->with($this->postCode)->willReturn(false);
        $this->assertFalse($this->useCase->isAllowed($this->customerGroupId, $this->country, $this->postCode));
    }

    /**
     * @test
     */
    public function itShouldReturnTrueIfTheCustomerMayOrder()
    {
        $this->mockRule->expects($this->once())->method('isPostCodeAllowed')->with($this->postCode)->willReturn(true);
        $this->useCase->isAllowed($this->customerGroupId, $this->country, $this->postCode);
    }
}
