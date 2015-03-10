<?php


namespace VinaiKopp\PostCodeFilter;


class RuleToDelete
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

    /**
     * @return CustomerGroupId
     */
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
