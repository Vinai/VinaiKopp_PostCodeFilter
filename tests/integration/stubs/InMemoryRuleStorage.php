<?php

namespace VinaiKopp\PostCodeFilter;

class InMemoryRuleStorage implements RuleStorage
{
    /**
     * @var array[]
     */
    private $rules = [];
    
    /**
     * @param int $customerGroupId
     * @param string $country
     * @return string[]
     */
    public function findPostCodesByGroupAndCountry($customerGroupId, $country)
    {
        if (! array_key_exists($customerGroupId, $this->rules)) {
            return [];
        }
        if (! array_key_exists($country, $this->rules[$customerGroupId])) {
            return [];
        }
        return $this->rules[$customerGroupId][$country];
    }

    /**
     * @param RuleToAdd $ruleToAdd
     * @return void
     */
    public function create(RuleToAdd $ruleToAdd)
    {
        $this->setRule(
            $ruleToAdd->getCustomerGroupIdValue(),
            $ruleToAdd->getCountryValue(),
            $ruleToAdd->getPostCodeValues()
        );
    }

    /**
     * @param RuleToUpdate $ruleToUpdate
     * @return void
     */
    public function update(RuleToUpdate $ruleToUpdate)
    {
        $this->unsetRule($ruleToUpdate->getOldCustomerGroupIdValue(), $ruleToUpdate->getOldCountryValue());

        $this->setRule(
            $ruleToUpdate->getNewCustomerGroupIdValue(),
            $ruleToUpdate->getNewCountryValue(),
            $ruleToUpdate->getNewPostCodeValues()
        );
    }

    /**
     * @param RuleToDelete $ruleToDelete
     * @return void
     */
    public function delete(RuleToDelete $ruleToDelete)
    {
        $this->unsetRule($ruleToDelete->getCustomerGroupIdValue(), $ruleToDelete->getCountryValue());
    }

    /**
     * @param int $groupId
     * @param string $country
     */
    private function unsetRule($groupId, $country)
    {
        unset($this->rules[$groupId][$country]);
    }

    /**
     * @param int $groupId
     * @param string $country
     * @param string[] $postCodes
     */
    private function setRule($groupId, $country, $postCodes)
    {
        $this->rules[$groupId][$country] = $postCodes;
    }
}
