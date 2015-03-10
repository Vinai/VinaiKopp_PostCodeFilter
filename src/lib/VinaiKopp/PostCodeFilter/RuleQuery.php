<?php


namespace VinaiKopp\PostCodeFilter;


class RuleQuery
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
     * @return int
     */
    public function getCustomerGroupIdValue()
    {
        return $this->customerGroupId->getValue();
    }

    /**
     * @return string
     */
    public function getCountryValue()
    {
        return $this->country->getValue();
    }
}
