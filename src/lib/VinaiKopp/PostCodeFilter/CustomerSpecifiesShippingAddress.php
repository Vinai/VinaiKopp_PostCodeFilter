<?php

namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\Rule\Components\Country;
use VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupId;
use VinaiKopp\PostCodeFilter\Storage\ReadModel\RuleReader;
use VinaiKopp\PostCodeFilter\Storage\ReadModel\RuleSpecByCountryAndGroupId;

class CustomerSpecifiesShippingAddress
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
    public function isAllowedDestination($customerGroupId, $iso2country, $postCode)
    {
        $spec = new RuleSpecByCountryAndGroupId(
            CustomerGroupId::fromInt($customerGroupId),
            Country::fromIso2Code($iso2country)
        );
        $rule = $this->ruleReader->findByCountryAndGroupId($spec);
        return $rule->isPostCodeAllowed($postCode);
    }
}
