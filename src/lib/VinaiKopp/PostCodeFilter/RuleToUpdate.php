<?php


namespace VinaiKopp\PostCodeFilter;


class RuleToUpdate
{
    /**
     * @var CustomerGroupId
     */
    private $oldCustomerGroupId;

    /**
     * @var Country
     */
    private $oldCountry;
    
    /**
     * @var RuleToAdd
     */
    private $newRule;

    public function __construct(CustomerGroupId $oldCustomerGroupId, Country $oldCountry, RuleToAdd $newRule)
    {
        $this->oldCustomerGroupId = $oldCustomerGroupId;
        $this->oldCountry = $oldCountry;
        $this->newRule = $newRule;
    }

    /**
     * @return int
     */
    public function getOldCustomerGroupIdValue()
    {
        return $this->oldCustomerGroupId->getValue();
    }

    /**
     * @return Country
     */
    public function getOldCountry()
    {
        return $this->oldCountry;
    }

    /**
     * @return string
     */
    public function getOldCountryValue()
    {
        return $this->oldCountry->getValue();
    }

    /**
     * @return CustomerGroupId
     */
    public function getOldCustomerGroupId()
    {
        return $this->oldCustomerGroupId;
    }

    /**
     * @return int
     */
    public function getNewCustomerGroupIdValue()
    {
        return $this->newRule->getCustomerGroupIdValue();
    }

    /**
     * @return string
     */
    public function getNewCountryValue()
    {
        return $this->newRule->getCountryValue();
    }

    /**
     * @return string[]|int[]
     */
    public function getNewPostCodeValues()
    {
        return $this->newRule->getPostCodeValues();
    }

    /**
     * @return bool
     */
    public function isGroupOrCountryChanged()
    {
        return
            $this->oldCustomerGroupId->getValue() != $this->newRule->getCustomerGroupIdValue() ||
            $this->oldCountry->getValue() != $this->newRule->getCountryValue();
    }

    /**
     * @return CustomerGroupId
     */
    public function getNewCustomerGroupId()
    {
        return $this->newRule->getCustomerGroupId();
    }

    /**
     * @return Country
     */
    public function getNewCountry()
    {
        return $this->newRule->getCountry();
    }
}
