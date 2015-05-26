<?php

namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\Rule\Components\Country;
use VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\Rule\Components\PostCodeList;
use VinaiKopp\PostCodeFilter\Storage\RuleRepositoryReader;
use VinaiKopp\PostCodeFilter\Storage\RuleRepositoryWriter;
use VinaiKopp\PostCodeFilter\Storage\WriteModel\RuleToAdd;

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
        $storage = new InMemoryRuleStorage();
        $repositoryWriter = new RuleRepositoryWriter($storage);
        $repositoryReader = new RuleRepositoryReader($storage);

        $repositoryWriter->createRule(new RuleToAdd(
            CustomerGroupIdList::fromArray([$this->generalGroupId, $this->guestGroupId]),
            Country::fromIso2Code($this->country),
            PostCodeList::fromArray([$this->allowedPostCode])
        ));
        $this->customerUseCase = new CustomerSpecifiesShippingAddress($repositoryReader);
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
        $this->assertTrue($this->customerUseCase->isAllowedDestination($groupId, $this->country, $this->allowedPostCode));
    }
    
    /**
     * @test
     */
    public function testCustomerWithoutAllowedPostCodeMayNotOrder()
    {
        $isAllowed = $this->customerUseCase->isAllowedDestination($this->generalGroupId, $this->country, $this->forbiddenPostCode);
        $this->assertFalse($isAllowed);
    }

    /**
     * @test
     */
    public function testResellerMayOrder()
    {
        $this->assertTrue($this->customerUseCase->isAllowedDestination($this->resellerGroupId, $this->country, 123));
    }
}
