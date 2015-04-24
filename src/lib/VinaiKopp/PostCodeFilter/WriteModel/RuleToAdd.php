<?php

namespace VinaiKopp\PostCodeFilter\WriteModel;

use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\RuleComponents\PostCodeList;

class RuleToAdd
{
    /**
     * @var CustomerGroupIdList
     */
    private $customerGroupIds;

    /**
     * @var string
     */
    private $country;

    /**
     * @var PostCodeList
     */
    private $postCodes;

    public function __construct(CustomerGroupIdList $customerGroupIds, Country $country, PostCodeList $postCodes)
    {
        $this->customerGroupIds = $customerGroupIds;
        $this->country = $country;
        $this->postCodes = $postCodes;
    }

    /**
     * @param int[] $customerGroupIds
     * @param string $iso2Country
     * @param string[] $postCodes
     * @return RuleToAdd
     */
    public static function createFromScalars(array $customerGroupIds, $iso2Country, array $postCodes)
    {
        return new self(
            CustomerGroupIdList::fromArray($customerGroupIds),
            Country::fromIso2Code($iso2Country),
            PostCodeList::fromArray($postCodes)
        );
    }

    /**
     * @return CustomerGroupIdList
     */
    public function getCustomerGroupIds()
    {
        return $this->customerGroupIds;
    }

    /**
     * @return int[]
     */
    public function getCustomerGroupIdValues()
    {
        return $this->customerGroupIds->getValues();
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getCountryValue()
    {
        return $this->country->getValue();
    }

    /**
     * @return string[]
     */
    public function getPostCodeValues()
    {
        return $this->postCodes->getValues();
    }
}
