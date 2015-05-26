<?php

namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\Rule\Components\Country;
use VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\Rule\Rule;
use VinaiKopp\PostCodeFilter\Storage\ReadModel\RuleReader;
use VinaiKopp\PostCodeFilter\Storage\ReadModel\RuleSpecByCountryAndGroupIds;

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
