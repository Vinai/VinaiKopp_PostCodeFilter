<?php

namespace VinaiKopp\PostCodeFilter\UseCases;

use VinaiKopp\PostCodeFilter\ReadModel\RuleReader;
use VinaiKopp\PostCodeFilter\ReadModel\Rule;

class AdminViewsRuleList
{
    /**
     * @var RuleReader
     */
    private $ruleReader;

    /**
     * @var callable[]
     */
    private $filters = [];

    public function __construct(RuleReader $ruleReader)
    {
        $this->ruleReader = $ruleReader;
    }

    /**
     * @return Rule[]
     */
    public function fetchAllRules()
    {
        $allRules = $this->ruleReader->findAll();
        return $this->applyFilters($allRules);
    }

    /**
     * @param string $filterString
     */
    public function setCountryFilter($filterString)
    {
        $this->filters[] = function (Rule $rule) use ($filterString) {
            return '' === $filterString || strpos($rule->getCountryValue(), $filterString) !== false;
        };
    }

    /**
     * @param string $filterString
     */
    public function setCustomerGroupIdFilter($filterString)
    {
        $this->filters[] = function (Rule $rule) use ($filterString) {
            return '' === $filterString || in_array($filterString, $rule->getCustomerGroupIdValues());
        };
    }

    /**
     * @param string $filterString
     */
    public function setPostCodeFilter($filterString)
    {
        $isMatchingPostCode = function ($flag, $postCode) use ($filterString) {
            return $flag || strpos($postCode, $filterString) !== false;
        };
        $hasMatchingPostCode = function(array $postCodeList) use ($isMatchingPostCode) {
            return array_reduce($postCodeList, $isMatchingPostCode, false);
        };
        $this->filters[] = function (Rule $rule) use ($filterString, $hasMatchingPostCode) {
            return '' === $filterString || $hasMatchingPostCode($rule->getPostCodeValues());
        };
    }

    /**
     * @param Rule[] $unfilteredRules
     * @return Rule[]
     */
    private function applyFilters(array $unfilteredRules)
    {
        return array_reduce($this->filters, function (array $remainingRules, \Closure $filterToApply) {
            return array_filter($remainingRules, $filterToApply);
        }, $unfilteredRules);
    }
}
