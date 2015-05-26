<?php

namespace VinaiKopp\PostCodeFilter\Storage;

use VinaiKopp\PostCodeFilter\Exceptions\NonMatchingRecordInResultException;
use VinaiKopp\PostCodeFilter\Rule\Components\Country;
use VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupId;
use VinaiKopp\PostCodeFilter\Rule\Components\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\Rule\Components\PostCodeList;
use VinaiKopp\PostCodeFilter\Rule\ExistingRule;
use VinaiKopp\PostCodeFilter\Rule\NonexistentRule;
use VinaiKopp\PostCodeFilter\Rule\Rule;
use VinaiKopp\PostCodeFilter\Storage\ReadModel\RuleReader;
use VinaiKopp\PostCodeFilter\Storage\ReadModel\RuleSpecByCountryAndGroupId;
use VinaiKopp\PostCodeFilter\Storage\ReadModel\RuleSpecByCountryAndGroupIds;

class RuleRepository implements RuleReader
{
    /**
     * @var RuleStorage
     */
    private $storage;

    public function __construct(RuleStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param RuleSpecByCountryAndGroupId $ruleSpec
     * @return Rule
     */
    public function findByCountryAndGroupId(RuleSpecByCountryAndGroupId $ruleSpec)
    {
        $postCodes = $this->storage->findPostCodesByCountryAndGroupId(
            $ruleSpec->getCountryValue(), $ruleSpec->getCustomerGroupIdValue()
        );
        if (empty($postCodes)) {
            return new NonexistentRule($ruleSpec->getCountry());
        }
        return $this->createRuleFoundInstance(
            [$ruleSpec->getCustomerGroupIdValue()],
            $ruleSpec->getCountryValue(),
            $postCodes
        );
    }

    /**
     * @param RuleSpecByCountryAndGroupIds $ruleSpec
     * @return Rule
     */
    public function findByCountryAndGroupIds(RuleSpecByCountryAndGroupIds $ruleSpec)
    {
        $records = $this->storage->findRulesByCountryAndGroupIds(
            $ruleSpec->getCountryValue(), $ruleSpec->getCustomerGroupIdValues()
        );
        if (empty($records)) {
            return new NonexistentRule($ruleSpec->getCountry());
        }
        return $this->combineQueryByCountryAndGroupIdsResultRecords($ruleSpec, $records);
    }

    /**
     * @return Rule[]
     */
    public function findAll()
    {
        return $this->convertRecordsToRuleInstances($this->storage->findAllRules());
    }

    /**
     * @param array[] $records
     * @return ExistingRule[]
     */
    private function convertRecordsToRuleInstances(array $records)
    {
        $combinedRecords = $this->mergeMatchingCountryAndPostcodes($records);
        return array_map(function (array $record) {
            return $this->createRuleFoundInstance($record['customer_group_ids'], $record['country'],
                $record['post_codes']);
        }, $combinedRecords);
    }

    /**
     * @param int[]|CustomerGroupId[] $customerGroupIds
     * @param string $country
     * @param string[]|int[] $postCodes
     * @return ExistingRule
     */
    private function createRuleFoundInstance(array $customerGroupIds, $country, array $postCodes)
    {
        return new ExistingRule(
            CustomerGroupIdList::fromArray($customerGroupIds),
            Country::fromIso2Code($country),
            PostCodeList::fromArray($postCodes)
        );
    }

    /**
     * @param RuleSpecByCountryAndGroupIds $ruleSpec
     * @param mixed[] $records
     * @return ExistingRule
     */
    private function combineQueryByCountryAndGroupIdsResultRecords(
        RuleSpecByCountryAndGroupIds $ruleSpec,
        array $records
    ) {
        $customerGroupIds = [];
        $postCodes = null;
        foreach ($records as $record) {
            $this->validateRuleSpecByCountryAndGroupIdsMatchesResultRecord($ruleSpec, $record);
            $customerGroupIds[] = $record['customer_group_id'];
            $postCodes = $record['post_codes'];
        }
        return $this->createRuleFoundInstance($customerGroupIds, $ruleSpec->getCountryValue(), $postCodes);
    }


    /**
     * @param RuleSpecByCountryAndGroupIds $ruleSpec
     * @param mixed[] $record
     */
    private function validateRuleSpecByCountryAndGroupIdsMatchesResultRecord(
        RuleSpecByCountryAndGroupIds $ruleSpec,
        array $record
    ) {
        $this->validateCountryMatchesSpec($record['country'], $ruleSpec);
        $this->validateCustomerGroupIdMatchesSpec($record['customer_group_id'], $ruleSpec);
    }

    /**
     * @param string $country
     * @param RuleSpecByCountryAndGroupIds $ruleSpec
     * @throws NonMatchingRecordInResultException
     */
    private function validateCountryMatchesSpec($country, RuleSpecByCountryAndGroupIds $ruleSpec)
    {
        if ($country != $ruleSpec->getCountryValue()) {
            throw new NonMatchingRecordInResultException(sprintf(
                'The country "%s" does not match the query country value "%s"',
                $country, $ruleSpec->getCountryValue()
            ));
        }
    }

    /**
     * @param int $customerGroupId
     * @param RuleSpecByCountryAndGroupIds $ruleSpec
     * @throws NonMatchingRecordInResultException
     */
    private function validateCustomerGroupIdMatchesSpec($customerGroupId, RuleSpecByCountryAndGroupIds $ruleSpec)
    {
        if (!in_array($customerGroupId, $ruleSpec->getCustomerGroupIdValues())) {
            throw new NonMatchingRecordInResultException(sprintf(
                'The customer group ID "%s" does not match the query customer group ID values "%s"',
                $customerGroupId,
                implode(', ', $ruleSpec->getCustomerGroupIdValues())
            ));
        }
    }
    
    /**
     * @param mixed[] $records
     * @return mixed[]
     */
    private function mergeMatchingCountryAndPostcodes(array $records)
    {
        $aggregate = $mergeResult = [];
        foreach ($records as $record) {
            sort($record['post_codes']);
            $postCodeKey = implode(',', $record['post_codes']);
            if (!isset($aggregate[$record['country']])) {
                $aggregate[$record['country']] = [];
            }
            if (array_key_exists($postCodeKey, $aggregate[$record['country']])) {
                $aggregate[$record['country']][$postCodeKey]['customer_group_ids'][] = $record['customer_group_id'];
            } else {
                $aggregate[$record['country']][$postCodeKey] = [
                    'country' => $record['country'],
                    'customer_group_ids' => [$record['customer_group_id']],
                    'post_codes' => $record['post_codes']
                ];
                $mergeResult[] =& $aggregate[$record['country']][$postCodeKey];
            }
        }
        return $mergeResult;
    }
}
