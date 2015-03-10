<?php


namespace VinaiKopp\PostCodeFilter;


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

    /**
     * @return string[]
     */
    public function getPostCodeValues()
    {
        return $this->postCodes->getValues();
    }

    /**
     * @return CustomerGroupId
     */
    public function getCustomerGroupId()
    {
        return $this->customerGroupId;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }
}
