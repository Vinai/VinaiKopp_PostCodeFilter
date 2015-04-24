<?php

namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\WriteModel\RuleToAdd;
use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\RuleComponents\PostCodeList;
use VinaiKopp\PostCodeFilter\UseCases\CustomerSpecifiesShippingAddress;

/**
 * @coversNothing
 */
class CustomerUseCaseEdge2EdgeTest extends \PHPUnit_Framework_TestCase
{
    private $forbiddenPostCode = '888';

    private $allowedPostCode = '999';
    
    private $country = 'DE';

    private $guestGroupId = 0;

    private $generalGroupId = 1;

    private $resellerGroupId = 6;

    /**
     * @var CustomerSpecifiesShippingAddress
     */
    private $customerUseCase;

    protected function setUp()
    {
        $repository = new RuleRepository(new InMemoryRuleStorage());

        $repository->createRule(new RuleToAdd(
            CustomerGroupIdList::fromArray([$this->generalGroupId, $this->guestGroupId]),
            Country::fromIso2Code($this->country),
            PostCodeList::fromArray([$this->allowedPostCode])
        ));
        $this->customerUseCase = new CustomerSpecifiesShippingAddress($repository);
    }
    
    public function allowedCustomerGroupIdProvider()
    {
        return [
            'general group id' => [$this->generalGroupId],
            'guest group id' => [$this->guestGroupId],
        ];
    }

    /**
     * @test
     * @dataProvider allowedCustomerGroupIdProvider
     * @param int $groupId
     */
    public function testCustomerWithAllowedPostCodeMayOrder($groupId)
    {
        $this->assertTrue($this->customerUseCase->isAllowed($groupId, $this->country, $this->allowedPostCode));
    }
    
    /**
     * @test
     */
    public function testCustomerWithoutAllowedPostCodeMayNotOrder()
    {
        $isAllowed = $this->customerUseCase->isAllowed($this->generalGroupId, $this->country, $this->forbiddenPostCode);
        $this->assertFalse($isAllowed);
    }

    /**
     * @test
     */
    public function testResellerMayOrder()
    {
        $this->assertTrue($this->customerUseCase->isAllowed($this->resellerGroupId, $this->country, 123));
    }
}
