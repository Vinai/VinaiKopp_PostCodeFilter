<?php

namespace VinaiKopp\PostCodeFilter\ReadModel;

use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;

class RuleSpecByCountryAndGroupIds
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
