<?php

namespace VinaiKopp\PostCodeFilter\Rule;


use VinaiKopp\PostCodeFilter\Rule\Components\Country;
use VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\Rule\Components\PostCodeList;

class NonexistentRule implements Rule
{
    /**
     * @var Country
     */
    private $country;

    public function __construct(Country $country)
    {
        $this->country = $country;
    }
    
    /**
     * @param string $postCode
     * @return bool
     */
    public function isPostCodeAllowed($postCode)
    {
        return true;
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
        return CustomerGroupIdList::fromArray([]);
    }

    /**
     * @return int[]
     */
    public function getCustomerGroupIdValues()
    {
        return [];
    }

    /**
     * @return PostCodeList
     */
    public function getPostCodes()
    {
        return PostCodeList::fromArray([]);
    }

    /**
     * @return string[]|int[]
     */
    public function getPostCodeValues()
    {
        return [];
    }
}
