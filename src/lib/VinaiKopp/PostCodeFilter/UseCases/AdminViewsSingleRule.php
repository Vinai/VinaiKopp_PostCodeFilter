<?php

namespace VinaiKopp\PostCodeFilter\UseCases;

use VinaiKopp\PostCodeFilter\Query\RuleSpecByCountryAndGroupIds;
use VinaiKopp\PostCodeFilter\Query\RuleReader;
use VinaiKopp\PostCodeFilter\Query\Rule;
use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;

class AdminViewsSingleRule
{
    /**
     * @var RuleReader
     */
    private $reader;

    public function __construct(RuleReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param $iso2country
     * @param array $customerGroupIds
     * @return Rule
     */
    public function fetchRule($iso2country, array $customerGroupIds)
    {
        $ruleSpec = $this->createRuleSpec($iso2country, array_map([$this, 'convertToInt'], $customerGroupIds));
        return $this->reader->findByCountryAndGroupIds($ruleSpec);
    }

    /**
     * @param string $iso2country
     * @param int[] $customerGroupIds
     * @return RuleSpecByCountryAndGroupIds
     */
    private function createRuleSpec($iso2country, array $customerGroupIds)
    {
        $ruleSpec = new RuleSpecByCountryAndGroupIds(
            Country::fromIso2Code($iso2country),
            CustomerGroupIdList::fromArray($customerGroupIds)
        );
        return $ruleSpec;
    }

    /**
     * @param mixed $value
     * @return int
     */
    private function convertToInt($value)
    {
        return (int) $value;
    }
}
