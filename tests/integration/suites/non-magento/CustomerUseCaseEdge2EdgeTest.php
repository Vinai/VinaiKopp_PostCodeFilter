<?php


namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\UseCases\CustomerSetsAddress;

class CustomerUseCaseEdge2EdgeTest extends \PHPUnit_Framework_TestCase
{
    private $forbiddenPostCode = '888';

    private $allowedPostCode = '999';
    
    private $country = 'DE';

    private $guestGroupId = 0;

    private $generalGroupId = 1;

    private $resellerGroupId = 6;

    /**
     * @var CustomerSetsAddress
     */
    private $customerUseCase;

    protected function setUp()
    {
        $repository = new RuleRepository(new InMemoryRuleStorage());
        
        foreach ($this->allowedCustomerGroupIdProvider() as $groupId) {
            $repository->createRule(new RuleToAdd(
                CustomerGroupId::fromInt($groupId[0]),
                Country::fromCode($this->country),
                PostCodeList::fromArray([$this->allowedPostCode])
            ));
        }        
        $this->customerUseCase = new CustomerSetsAddress($repository);
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
     */
    public function testCustomerWithAllowedPostCodeMayOrder($groupId)
    {
        $this->assertTrue($this->customerUseCase->mayOrder($groupId, $this->country, $this->allowedPostCode));
    }
    
    /**
     * @test
     */
    public function testCustomerWithoutAllowedPostCodeMayNotOrder()
    {
        $mayOrder = $this->customerUseCase->mayOrder($this->generalGroupId, $this->country, $this->forbiddenPostCode);
        $this->assertFalse($mayOrder);
    }

    /**
     * @test
     */
    public function testResellerMayOrder()
    {
        $this->assertTrue($this->customerUseCase->mayOrder($this->resellerGroupId, $this->country, 123));
    }
}
