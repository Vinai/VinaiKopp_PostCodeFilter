<?php


namespace VinaiKopp\PostCodeFilter\Query;


use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId;

class RuleSpecByCountryAndGroupId
{
    /**
     * @var CustomerGroupId
     */
    private $customerGroupId;
    
    /**
     * @var Country
     */
    private $country;

    public function __construct(CustomerGroupId $customerGroupId, Country $country)
    {
        $this->customerGroupId = $customerGroupId;
        $this->country = $country;
    }

    public function getCustomerGroupId()
    {
        return $this->customerGroupId;
    }

    /**
     * @return int
     */
    public function getCustomerGroupIdValue()
    {
        return $this->customerGroupId->getValue();
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
}
