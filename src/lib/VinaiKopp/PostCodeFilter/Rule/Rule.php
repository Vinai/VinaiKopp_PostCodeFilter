<?php

namespace VinaiKopp\PostCodeFilter\Rule;

use VinaiKopp\PostCodeFilter\Rule\Components\Country;
use VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\Rule\Components\PostCodeList;

interface Rule
{
    /**
     * @param string $postCode
     * @return bool
     */
    public function isPostCodeAllowed($postCode);

    /**
     * @return Country
     */
    public function getCountry();

    /**
     * @return string
     */
    public function getCountryValue();

    /**
     * @return CustomerGroupIdList
     */
    public function getCustomerGroupIds();

    /**
     * @return int[]
     */
    public function getCustomerGroupIdValues();

    /**
     * @return PostCodeList
     */
    public function getPostCodes();

    /**
     * @return string[]|int[]
     */
    public function getPostCodeValues();
}
