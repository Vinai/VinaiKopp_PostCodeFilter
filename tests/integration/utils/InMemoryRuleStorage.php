<?php

namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\Storage\RuleStorage;

class InMemoryRuleStorage implements RuleStorage
{
    /**
     * @var array[]
     */
    private $rules = [];

    /**
     * @var null|array[]
     */
    private $snapshot;

    /**
     * @param string $country
     * @param int $customerGroupId
     * @return string[]
     */
    public function findPostCodesByCountryAndGroupId($country, $customerGroupId)
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
     * @param string $country
     * @param int[] $customerGroupIds
     * @return array[]
     */
    public function findRulesByCountryAndGroupIds($country, array $customerGroupIds)
    {
        $result = [];
        
        foreach ($this->rules as $customerGroupId => $rule) {
            if (isset($rule[$country])) {
               $result[] = [
                   'customer_group_id' => $customerGroupId,
                   'country' => $country,
                   'post_codes' => $rule[$country]
               ];
            }
        }
        return $result;
    }

    /**
     * @return mixed[]
     */
    public function findAllRules()
    {
        $result = [];
        
        foreach ($this->rules as $customerGroupId => $rule) {
            foreach ($rule as $country => $postcodes) {
                $result[] = [
                    'customer_group_id' => $customerGroupId,
                    'country' => $country,
                    'post_codes' => $postcodes
                ];
            }
        }
        
        return $result;
    }

    /**
     * @param string $iso2country
     * @param int $customerGroupId
     * @param array $postCodes
     */
    public function create($iso2country, $customerGroupId, array $postCodes)
    {
        $this->setRule($customerGroupId, $iso2country, $postCodes);
    }

    /**
     * @param string $iso2country
     * @param int $customerGroupId
     */
    public function delete($iso2country, $customerGroupId)
    {
        $this->unsetRule($customerGroupId, $iso2country);
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

    public function beginTransaction()
    {
        $this->snapshot = $this->rules;
    }

    public function commitTransaction()
    {
        $this->snapshot = null;
    }

    public function rollbackTransaction()
    {
        $this->rules = $this->snapshot;
    }
}
