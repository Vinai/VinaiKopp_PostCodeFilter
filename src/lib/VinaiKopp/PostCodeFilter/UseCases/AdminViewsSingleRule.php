<?php


namespace VinaiKopp\PostCodeFilter\UseCases;


use VinaiKopp\PostCodeFilter\Query\QueryByCountryAndGroupIds;
use VinaiKopp\PostCodeFilter\Query\RuleReader;
use VinaiKopp\PostCodeFilter\Query\RuleResult;
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
     * @return RuleResult
     */
    public function fetchRule($iso2country, array $customerGroupIds)
    {
        $query = $this->createQuery($iso2country, array_map([$this, 'convertToInt'], $customerGroupIds));
        return $this->reader->findByCountryAndGroupIds($query);
    }

    /**
     * @param string $iso2country
     * @param int[] $customerGroupIds
     * @return QueryByCountryAndGroupIds
     */
    private function createQuery($iso2country, array $customerGroupIds)
    {
        $query = new QueryByCountryAndGroupIds(
            Country::fromIso2Code($iso2country),
            CustomerGroupIdList::fromArray($customerGroupIds)
        );
        return $query;
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
