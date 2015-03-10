<?php

namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\UseCase\AdminAddsRule;
use VinaiKopp\PostCodeFilter\UseCase\AdminDeletesRule;
use VinaiKopp\PostCodeFilter\UseCase\AdminUpdatesRule;

class AdminUseCasesEdge2EdgeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CustomerGroupId
     */
    private $customerGroupId;

    /**
     * @var CustomerGroupId
     */
    private $newCustomerGroupId;

    /**
     * @var Country
     */
    private $country;

    /**
     * @var Country
     */
    private $newCountry;

    /**
     * @var string[]
     */
    private $postCodes;

    /**
     * @var string[]
     */
    private $newPostCodes;

    protected function setUp()
    {
        $this->customerGroupId = CustomerGroupId::fromInt(5);
        $this->newCustomerGroupId = CustomerGroupId::fromInt($this->customerGroupId->getValue() + 1);
        
        $this->country = Country::fromCode('NZ');
        $this->newCountry = Country::fromCode('GB');
        
        $this->postCodes = ['121', '131', '141', '151'];
        $this->newPostCodes = ['222', '333', '444', '555'];
    }
    /**
     * @test
     */
    public function itShouldAddARule()
    {
        $storage = new InMemoryRuleStorage();
        $addRuleUseCase = new AdminAddsRule(new RuleRepository($storage));

        $ruleToAdd = new RuleToAdd(
            $this->customerGroupId,
            $this->country,
            PostCodeList::fromArray($this->postCodes)
        );

        $this->assertRuleNotInStorage($storage, $this->customerGroupId, $this->country);
        
        $addRuleUseCase->addRule($ruleToAdd);
        
        $this->assertRuleInStorage($storage, $this->customerGroupId, $this->country);
        $this->assertPostCodesAllowed($storage, $this->customerGroupId, $this->country, $this->postCodes);
        
        return $storage;
    }

    /**
     * @test
     * @depends itShouldAddARule
     * @param RuleStorage $storage
     * @return RuleStorage
     */
    public function itShouldUpdateARule(RuleStorage $storage)
    {
        $updateRuleUseCase = new AdminUpdatesRule(new RuleRepository($storage));
        
        $newRule = new RuleToAdd(
            $this->newCustomerGroupId,
            $this->newCountry,
            PostCodeList::fromArray($this->newPostCodes)
        );
        $ruleToUpdate = new RuleToUpdate($this->customerGroupId, $this->country, $newRule);

        $this->assertRuleInStorage($storage, $this->customerGroupId, $this->country);
        
        $updateRuleUseCase->updateRule($ruleToUpdate);
        
        $this->assertRuleNotInStorage($storage, $this->customerGroupId, $this->country);
        $this->assertRuleInStorage($storage, $this->newCustomerGroupId, $this->newCountry);
        $this->assertPostCodesAllowed($storage, $this->newCustomerGroupId, $this->newCountry, $this->newPostCodes);
        
        return $storage;
    }

    /**
     * @test
     * @depends itShouldUpdateARule
     * @param RuleStorage $storage
     */
    public function itShouldDeleteARule(RuleStorage $storage)
    {
        $ruleToDelete = new RuleToDelete(
            $this->newCustomerGroupId,
            $this->newCountry
        );
        $deleteRuleUseCase = new AdminDeletesRule(new RuleRepository($storage));
        
        $this->assertRuleInStorage($storage, $this->newCustomerGroupId, $this->newCountry);
        
        $deleteRuleUseCase->deleteRule($ruleToDelete);

        $this->assertRuleNotInStorage($storage, $this->newCustomerGroupId, $this->newCountry);
    }

    /**
     * @param RuleStorage $storage
     * @param CustomerGroupId $groupId
     * @param Country $country
     * @param string[]|int[] $postCodes
     */
    private function assertPostCodesAllowed(
        RuleStorage $storage,
        CustomerGroupId $groupId,
        Country $country,
        array $postCodes
    )
    {
        $ruleQuery = new RuleQuery($groupId, $country);
        $ruleFound = (new RuleRepository($storage))->findByGroupAndCountry($ruleQuery);
        foreach ($postCodes as $code) {
            $this->assertTrue($ruleFound->isPostCodeAllowed($code));
        }
    }
    
    private function assertRuleInStorage(
        RuleStorage $storage,
        CustomerGroupId $groupId,
        Country $country
    )
    {
        $ruleQuery = new RuleQuery($groupId, $country);
        $rule = (new RuleRepository($storage))->findByGroupAndCountry($ruleQuery);
        $this->assertInstanceOf(RuleFound::class, $rule);
    }
    
    private function assertRuleNotInStorage(
        RuleStorage $storage,
        CustomerGroupId $groupId,
        Country $country
    )
    {
        $ruleQuery = new RuleQuery($groupId, $country);
        $rule = (new RuleRepository($storage))->findByGroupAndCountry($ruleQuery);
        $this->assertInstanceOf(RuleNotFound::class, $rule);
    }
}

