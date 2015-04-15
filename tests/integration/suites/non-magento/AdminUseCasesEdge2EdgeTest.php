<?php

namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\Command\RuleToAdd;
use VinaiKopp\PostCodeFilter\Command\RuleToDelete;
use VinaiKopp\PostCodeFilter\Query\RuleSpecByCountryAndGroupIds;
use VinaiKopp\PostCodeFilter\Query\RuleFound;
use VinaiKopp\PostCodeFilter\Query\RuleNotFound;
use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\RuleComponents\PostCodeList;
use VinaiKopp\PostCodeFilter\UseCases\AdminAddsRule;
use VinaiKopp\PostCodeFilter\UseCases\AdminDeletesRule;
use VinaiKopp\PostCodeFilter\UseCases\AdminUpdatesRule;

/**
 * @coversNothing
 */
class AdminUseCasesEdge2EdgeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CustomerGroupIdList
     */
    private $customerGroupIds;

    /**
     * @var CustomerGroupIdList
     */
    private $newCustomerGroupIds;

    /**
     * @var Country
     */
    private $country;

    /**
     * @var Country
     */
    private $newCountry;

    /**
     * @var PostCodeList
     */
    private $postCodes;

    /**
     * @var PostCodeList
     */
    private $newPostCodes;

    protected function setUp()
    {
        $this->customerGroupIds = CustomerGroupIdList::fromArray([5]);
        $this->newCustomerGroupIds = CustomerGroupIdList::fromArray([6]);
        
        $this->country = Country::fromIso2Code('NZ');
        $this->newCountry = Country::fromIso2Code('GB');
        
        $this->postCodes = PostCodeList::fromArray(['121', '131', '141', '151']);
        $this->newPostCodes = PostCodeList::fromArray(['222', '333', '444', '555']);
    }
    /**
     * @test
     */
    public function itShouldAddARule()
    {
        $storage = new InMemoryRuleStorage();
        $ruleRepository = new RuleRepository($storage);
        $addRuleUseCase = new AdminAddsRule($ruleRepository, $ruleRepository);

        $ruleToAdd = new RuleToAdd($this->customerGroupIds, $this->country, $this->postCodes);

        $this->assertRuleNotInStorage($storage, $this->customerGroupIds, $this->country);
        
        $addRuleUseCase->addRule($ruleToAdd);
        
        $this->assertRuleInStorage($storage, $this->customerGroupIds, $this->country);
        $this->assertPostCodesAllowed($storage, $this->customerGroupIds, $this->country, $this->postCodes->getValues());
        
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
        $ruleRepository = new RuleRepository($storage);
        $updateRuleUseCase = new AdminUpdatesRule($ruleRepository, $ruleRepository);
        
        $ruleToDelete = new RuleToDelete($this->customerGroupIds, $this->country);
        $ruleToAdd = new RuleToAdd($this->newCustomerGroupIds, $this->newCountry, $this->newPostCodes);

        $this->assertRuleInStorage($storage, $this->customerGroupIds, $this->country);
        
        $updateRuleUseCase->updateRule($ruleToDelete, $ruleToAdd);

        $this->assertRuleNotInStorage($storage, $this->customerGroupIds, $this->country);
        $this->assertRuleInStorage($storage, $this->newCustomerGroupIds, $this->newCountry);
        
        return $storage;
    }

    /**
     * @test
     * @depends itShouldUpdateARule
     * @param RuleStorage $storage
     */
    public function itShouldDeleteARule(RuleStorage $storage)
    {
        $ruleRepository = new RuleRepository($storage);
        $deleteRuleUseCase = new AdminDeletesRule($ruleRepository, $ruleRepository);
        $ruleToDelete = new RuleToDelete($this->newCustomerGroupIds, $this->newCountry);
        
        $this->assertRuleInStorage($storage, $this->newCustomerGroupIds, $this->newCountry);
        
        $deleteRuleUseCase->deleteRule($ruleToDelete);

        $this->assertRuleNotInStorage($storage, $this->newCustomerGroupIds, $this->newCountry);
    }

    /**
     * @param RuleStorage $storage
     * @param CustomerGroupIdList $groupIds
     * @param Country $country
     * @param string[]|int[] $postCodes
     */
    private function assertPostCodesAllowed(
        RuleStorage $storage,
        CustomerGroupIdList $groupIds,
        Country $country,
        array $postCodes
    )
    {
        $ruleSpec = new RuleSpecByCountryAndGroupIds($country, $groupIds);
        $ruleFound = (new RuleRepository($storage))->findByCountryAndGroupIds($ruleSpec);
        foreach ($postCodes as $code) {
            $this->assertTrue($ruleFound->isPostCodeAllowed($code));
        }
    }
    
    private function assertRuleInStorage(
        RuleStorage $storage,
        CustomerGroupIdList $groupIds,
        Country $country
    )
    {
        $ruleSpec = new RuleSpecByCountryAndGroupIds($country, $groupIds);
        $rule = (new RuleRepository($storage))->findByCountryAndGroupIds($ruleSpec);
        $this->assertInstanceOf(RuleFound::class, $rule);
    }
    
    private function assertRuleNotInStorage(
        RuleStorage $storage,
        CustomerGroupIdList $groupId,
        Country $country
    )
    {
        $ruleSpec = new RuleSpecByCountryAndGroupIds($country, $groupId);
        $rule = (new RuleRepository($storage))->findByCountryAndGroupIds($ruleSpec);
        $this->assertInstanceOf(RuleNotFound::class, $rule);
    }
}

