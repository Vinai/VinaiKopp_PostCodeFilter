<?php

namespace VinaiKopp\PostCodeFilter;

interface RuleStorage
{
    /**
     * @param string $iso2country
     * @param int $customerGroupId
     * @return string[]
     */
    public function findPostCodesByCountryAndGroupId($iso2country, $customerGroupId);

    /**
     * @param string $iso2country
     * @param int[] $customerGroupIds
     * @return mixed[]
     * @see RuleRepository::findByCountryAndGroupIds()
     */
    public function findRulesByCountryAndGroupIds($iso2country, array $customerGroupIds);

    /**
     * @return mixed[]
     * @see RuleRepository::findAll()
     */
    public function findAllRules();

    /**
     * @param string $iso2country
     * @param int $customerGroupId
     * @param string[]|int[] $postCodes
     * @return void
     */
    public function create($iso2country, $customerGroupId, array $postCodes);

    /**
     * @param string $iso2country
     * @param int $customerGroupId
     * @return void
     */
    public function delete($iso2country, $customerGroupId);

    /**
     * @return void
     */
    public function beginTransaction();

    /**
     * @return void
     */
    public function commitTransaction();

    /**
     * @return void
     */
    public function rollbackTransaction();
}
