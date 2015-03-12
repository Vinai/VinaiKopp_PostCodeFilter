<?php


namespace VinaiKopp\PostCodeFilter\Command;


use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId;
use VinaiKopp\PostCodeFilter\RuleComponents\PostCodeList;

class RuleToAdd
{
    /**
     * @var int
     */
    private $customerGroupId;

    /**
     * @var string
     */
    private $country;

    /**
     * @var mixed[]
     */
    private $postCodes;

    /**
     * @param CustomerGroupId $customerGroupId
     * @param Country $country
     * @param PostCodeList $postCodes
     */
    public function __construct(CustomerGroupId $customerGroupId, Country $country, PostCodeList $postCodes)
    {
        $this->customerGroupId = $customerGroupId;
        $this->country = $country;
        $this->postCodes = $postCodes;
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

    /**
     * @return string[]
     */
    public function getPostCodeValues()
    {
        return $this->postCodes->getValues();
    }
}
