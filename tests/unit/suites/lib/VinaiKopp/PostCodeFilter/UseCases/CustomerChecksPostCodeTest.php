<?php

namespace VinaiKopp\PostCodeFilter\UseCases;

use VinaiKopp\PostCodeFilter\Query\RuleReader;
use VinaiKopp\PostCodeFilter\Query\RuleResult;

/**
 * @covers \VinaiKopp\PostCodeFilter\UseCases\CustomerChecksPostCode
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId
 * @uses   \VinaiKopp\PostCodeFilter\RuleComponents\Country
 * @uses   \VinaiKopp\PostCodeFilter\Query\QueryByCountryAndGroupId
 */
class CustomerChecksPostCodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CustomerChecksPostCode
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
     * @var RuleResult|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRule;

    protected function setUp()
    {
        $this->mockRule = $this->getMock(RuleResult::class);
        $this->mockRuleReader = $this->getMock(RuleReader::class);
        $this->mockRuleReader->expects($this->any())->method('findByCountryAndGroupId')->willReturn($this->mockRule);
        $this->useCase = new CustomerChecksPostCode($this->mockRuleReader);
    }
    
    /**
     * @test
     */
    public function itShouldReturnFalseIfTheCustomerMayNotOrder()
    {
        $this->mockRule->expects($this->once())->method('isPostCodeAllowed')->with($this->postCode)->willReturn(false);
        $this->assertFalse($this->useCase->mayOrder($this->customerGroupId, $this->country, $this->postCode));
    }

    /**
     * @test
     */
    public function itShouldReturnTrueIfTheCustomerMayOrder()
    {
        $this->mockRule->expects($this->once())->method('isPostCodeAllowed')->with($this->postCode)->willReturn(true);
        $this->useCase->mayOrder($this->customerGroupId, $this->country, $this->postCode);
    }
}
