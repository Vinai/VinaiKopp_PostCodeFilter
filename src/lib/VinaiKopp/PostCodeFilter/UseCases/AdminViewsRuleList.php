<?php

namespace VinaiKopp\PostCodeFilter\UseCases;

use VinaiKopp\PostCodeFilter\ReadModel\RuleReader;
use VinaiKopp\PostCodeFilter\ReadModel\Rule;

class AdminViewsRuleList
{
    const SORT_DESCENDING = 'DESC';
    const SORT_ASCENDING = 'ASC';
    
    /**
     * @var RuleReader
     */
    private $ruleReader;

    /**
     * @var callable[]
     */
    private $filters = [];

    /**
     * @var callable
     */
    private $sortOrder;

    public function __construct(RuleReader $ruleReader)
    {
        $this->ruleReader = $ruleReader;
    }

    /**
     * @return Rule[]
     */
    public function fetchRules()
    {
        $allRules = $this->ruleReader->findAll();
        return $this->applySorting($this->applyFilters($allRules));
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

    /**
     * @param string $ascOrDesc
     */
    public function sortByCountry($ascOrDesc)
    {
        $factor = $this->getSortDirectionFactor($ascOrDesc);
        $this->sortOrder = function(Rule $ruleA, Rule $ruleB) use ($factor) {
            return strnatcasecmp($ruleA->getCountryValue(), $ruleB->getCountryValue()) * $factor;
        };
    }

    /**
     * @param string $ascOrDesc
     */
    public function sortByCustomerGroupId($ascOrDesc)
    {
        $factor = $this->getSortDirectionFactor($ascOrDesc);
        $this->sortOrder = function(Rule $ruleA, Rule $ruleB) use ($factor) {
            $result = $this->compareArrays($ruleA->getCustomerGroupIdValues(), $ruleB->getCustomerGroupIdValues());
            return $result * $factor;
        };
    }

    /**
     * @param string $ascOrDesc
     */
    public function sortByPostCode($ascOrDesc)
    {
        $factor = $this->getSortDirectionFactor($ascOrDesc);
        $this->sortOrder = function(Rule $ruleA, Rule $ruleB) use ($factor) {
            $result = $this->compareArrays($ruleA->getPostCodeValues(), $ruleB->getPostCodeValues());
            return $result * $factor;
        };
    }

    /**
     * @param Rule[] $unsortedRules
     * @return Rule[]
     */
    private function applySorting(array $unsortedRules)
    {
        $rulesToSort = $unsortedRules;
        if ($this->sortOrder) {
            uasort($rulesToSort, $this->sortOrder);
        }
        return $rulesToSort;
    }

    /**
     * @param string $ascOrDesc
     * @return int
     */
    private function getSortDirectionFactor($ascOrDesc)
    {
        return strtoupper($ascOrDesc) === self::SORT_DESCENDING
            ? -1
            : 1;
    }

    private function compareArrays(array $valueA, array $valueB)
    {
        $arrayA = array_values($valueA);
        $arrayB = array_values($valueB);
        foreach ($arrayA as $i => $a) {
            if (!isset($arrayB[$i])) {
                return 1;
            }
            $result = strnatcasecmp($a, $arrayB[$i]);
            if ($result !== 0) {
                return $result;
            }
        }
        if (count($arrayB) > count($arrayA)) {
            return -1;
        }
        return 0;
    }
}
