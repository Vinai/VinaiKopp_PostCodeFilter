<?php


namespace VinaiKopp\PostCodeFilter\Query;


use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;

class QueryByCountryAndGroupIds
{
    /**
     * @var Country
     */
    private $country;
    
    /**
     * @var CustomerGroupIdList
     */
    private $customerGroupIds;

    public function __construct(Country $country, CustomerGroupIdList $customerGroupIds)
    {
        $this->country = $country;
        $this->customerGroupIds = $customerGroupIds;
    }

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
}
