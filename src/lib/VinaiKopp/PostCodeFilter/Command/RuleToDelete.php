<?php


namespace VinaiKopp\PostCodeFilter\Command;


use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;

class RuleToDelete
{
    /**
     * @var CustomerGroupIdList
     */
    private $customerGroupIds;
    
    /**
     * @var Country
     */
    private $country;

    public function __construct(CustomerGroupIdList $customerGroupIds, Country $country)
    {
        $this->customerGroupIds = $customerGroupIds;
        $this->country = $country;
    }

    /**
     * @param int[] $customerGroupIds
     * @param string $iso2country
     * @return RuleToDelete
     */
    public static function createFromScalars(array $customerGroupIds, $iso2country)
    {
        return new self(
            CustomerGroupIdList::fromArray($customerGroupIds),
            Country::fromIso2Code($iso2country)
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
}
