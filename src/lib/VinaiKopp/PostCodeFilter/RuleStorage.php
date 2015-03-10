<?php


namespace VinaiKopp\PostCodeFilter;


interface RuleStorage
{
    /**
     * @param int $customerGroupId
     * @param string $country
     * @return string[]
     */
    public function findPostCodesByGroupAndCountry($customerGroupId, $country);

    /**
     * @param RuleToAdd $ruleToAdd
     * @return void
     */
    public function create(RuleToAdd $ruleToAdd);

    /**
     * @param RuleToUpdate $ruleToUpdate
     * @return void
     */
    public function update(RuleToUpdate $ruleToUpdate);

    /**
     * @param RuleToDelete $ruleToDelete
     * @return void
     */
    public function delete(RuleToDelete $ruleToDelete);
}
