<?php

namespace VinaiKopp\PostCodeFilter\UseCase;

use VinaiKopp\PostCodeFilter\Rule;
use VinaiKopp\PostCodeFilter\RuleRepository;

/**
 * @covers \VinaiKopp\PostCodeFilter\UseCase\CustomerSetsAddress
 * @uses   \VinaiKopp\PostCodeFilter\CustomerGroupId
 * @uses   \VinaiKopp\PostCodeFilter\Country
 * @uses   \VinaiKopp\PostCodeFilter\RuleQuery
 */
class CustomerSetsAddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CustomerSetsAddress
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
     * @var RuleRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRuleRepository;

    /**
     * @var Rule|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockRule;

    protected function setUp()
    {
        $this->mockRule = $this->getMock(Rule::class);
        $this->mockRuleRepository = $this->getMock(RuleRepository::class, [], [], '', false);
        $this->mockRuleRepository->expects($this->any())->method('findByGroupAndCountry')->willReturn($this->mockRule);
        $this->useCase = new CustomerSetsAddress($this->mockRuleRepository);
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
