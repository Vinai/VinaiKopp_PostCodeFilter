<?php


namespace VinaiKopp\PostCodeFilter\RuleComponents;


class CustomerGroupIdList
{
    /**
     * @var CustomerGroupId[]
     */
    private $customerGroupIds;

    private function __construct(array $customerGroupIds)
    {
        $this->customerGroupIds = $customerGroupIds;
    }
    
    /**
     * @param int[]|CustomerGroupId[] $inputArray
     * @return CustomerGroupIdList
     */
    public static function fromArray(array $inputArray)
    {
        $customerGroups = array_map([__CLASS__, 'getCustomerGroupIdInstance'], $inputArray);
        return new self(array_values(array_unique($customerGroups, SORT_REGULAR)));
    }

    public function getCustomerGroupIds()
    {
        return $this->customerGroupIds;
    }

    /**
     * @return int[]
     */
    public function getValues()
    {
        return array_map(function (CustomerGroupId $customerGroupId) {
            return $customerGroupId->getValue();
        }, $this->customerGroupIds);
    }

    /**
     * @param int|CustomerGroupId $groupId
     * @return CustomerGroupId
     */
    private static function getCustomerGroupIdInstance($groupId)
    {
        if ($groupId instanceof CustomerGroupId) {
            return $groupId;
        }
        return CustomerGroupId::fromInt($groupId);
    }
}
