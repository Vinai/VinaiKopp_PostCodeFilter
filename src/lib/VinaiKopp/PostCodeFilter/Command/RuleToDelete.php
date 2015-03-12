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
