<?php


namespace VinaiKopp\PostCodeFilter\UseCases;

use VinaiKopp\PostCodeFilter\Query\QueryByCountryAndGroupId;
use VinaiKopp\PostCodeFilter\Query\RuleReader;
use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId;

class CustomerSetsAddress
{
    /**
     * @var RuleReader
     */
    private $ruleReader;

    public function __construct(RuleReader $ruleReader)
    {
        $this->ruleReader = $ruleReader;
    }
    
    /**
     * @param int $customerGroupId
     * @param string $iso2country
     * @param string $postCode
     * @return bool
     */
    public function mayOrder($customerGroupId, $iso2country, $postCode)
    {
        $query = new QueryByCountryAndGroupId(
            CustomerGroupId::fromInt($customerGroupId),
            Country::fromIso2Code($iso2country)
        );
        $rule = $this->ruleReader->findByCountryAndGroupId($query);
        return $rule->isPostCodeAllowed($postCode);
    }
}