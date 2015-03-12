<?php


namespace VinaiKopp\PostCodeFilter;


use VinaiKopp\PostCodeFilter\Command\RuleToAdd;
use VinaiKopp\PostCodeFilter\Command\RuleToDelete;

interface RuleStorage
{
    /**
     * @param string $country
     * @param int $customerGroupId
     * @return string[]
     */
    public function findPostCodesByCountryAndGroupId($country, $customerGroupId);

    /**
     * @param string $country
     * @param int[] $customerGroupIds
     * @return mixed[]
     */
    public function findRulesByCountryAndGroupIds($country, array $customerGroupIds);

    /**
     * @return mixed[]
     */
    public function findAllRules();

    /**
     * @param RuleToAdd $ruleToAdd
     * @return void
     */
    public function create(RuleToAdd $ruleToAdd);

    /**
     * @param RuleToDelete $ruleToDelete
     * @return void
     */
    public function delete(RuleToDelete $ruleToDelete);
}
