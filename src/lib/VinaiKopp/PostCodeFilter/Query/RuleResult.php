<?php

namespace VinaiKopp\PostCodeFilter\Query;

use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\RuleComponents\PostCodeList;

interface RuleResult
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
