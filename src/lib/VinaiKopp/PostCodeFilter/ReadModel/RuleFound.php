<?php

namespace VinaiKopp\PostCodeFilter\ReadModel;

use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\RuleComponents\PostCode;
use VinaiKopp\PostCodeFilter\RuleComponents\PostCodeList;

class RuleFound implements Rule
{
    /**
     * @var string[]
     */
    private $allowedPostCodes;
    
    /**
     * @var CustomerGroupId
     */
    private $customerGroupIds;
    
    /**
     * @var Country
     */
    private $country;

    public function __construct(
        CustomerGroupIdList $customerGroupIds,
        Country $country,
        PostCodeList $allowedPostCodes
    )
    {
        $this->customerGroupIds = $customerGroupIds;
        $this->country = $country;
        $this->allowedPostCodes = $allowedPostCodes;
    }
    
    /**
     * @param string|int|PostCode $input
     * @return bool
     */
    public function isPostCodeAllowed($input)
    {
        $postCode = $this->convertToPostCodeType($input);
        return $this->allowedPostCodes->contains($postCode);
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

    /**
     * @return PostCodeList
     */
    public function getPostCodes()
    {
        return $this->allowedPostCodes;
    }

    /**
     * @return int[]|string[]
     */
    public function getPostCodeValues()
    {
        return $this->allowedPostCodes->getValues();
    }
    
    /**
     * @param int|string|PostCode $input
     * @return PostCode
     */
    private function convertToPostCodeType($input)
    {
        if ($input instanceof PostCode) {
            return $input;
        } else {
            return PostCode::fromIntOrString($input);
        }
    }
}
