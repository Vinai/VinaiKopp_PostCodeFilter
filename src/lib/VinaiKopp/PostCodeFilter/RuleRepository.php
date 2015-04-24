<?php

namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\WriteModel\RuleToAdd;
use VinaiKopp\PostCodeFilter\WriteModel\RuleToDelete;
use VinaiKopp\PostCodeFilter\WriteModel\RuleWriter;
use VinaiKopp\PostCodeFilter\Exceptions\NonMatchingRecordInResultException;
use VinaiKopp\PostCodeFilter\ReadModel\RuleFound;
use VinaiKopp\PostCodeFilter\ReadModel\RuleNotFound;
use VinaiKopp\PostCodeFilter\ReadModel\RuleSpecByCountryAndGroupId;
use VinaiKopp\PostCodeFilter\ReadModel\RuleSpecByCountryAndGroupIds;
use VinaiKopp\PostCodeFilter\ReadModel\RuleReader;
use VinaiKopp\PostCodeFilter\ReadModel\Rule;
use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupId;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\RuleComponents\PostCodeList;

class RuleRepository implements RuleWriter, RuleReader
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
            return new RuleNotFound($ruleSpec->getCountry());
        }
        return $this->createRuleFoundInstance([$ruleSpec->getCustomerGroupIdValue()], $ruleSpec->getCountryValue(), $postCodes);
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
            return new RuleNotFound($ruleSpec->getCountry());
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

    public function createRule(RuleToAdd $ruleToAdd)
    {
        array_map(function($customerGroupId) use ($ruleToAdd) {
            $this->storage->create(
                $ruleToAdd->getCountryValue(),
                $customerGroupId,
                $ruleToAdd->getPostCodeValues()
            );
        }, $ruleToAdd->getCustomerGroupIdValues());
    }

    public function deleteRule(RuleToDelete $ruleToDelete)
    {
        array_map(function($customerGroupId) use ($ruleToDelete) {
            $this->storage->delete($ruleToDelete->getCountryValue(), $customerGroupId);
        }, $ruleToDelete->getCustomerGroupIdValues());
    }

    /**
     * @param array[] $records
     * @return RuleFound[]
     */
    private function convertRecordsToRuleInstances(array $records)
    {
        $combinedRecords = $this->mergeMatchingCountryAndPostcodes($records);
        return array_map(function (array $record) {
            return $this->createRuleFoundInstance($record['customer_group_ids'], $record['country'], $record['post_codes']);
        }, $combinedRecords);
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
            if (! isset($aggregate[$record['country']])) {
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

    /**
     * @param int[]|CustomerGroupId[] $customerGroupIds
     * @param string $country
     * @param string[]|int[] $postCodes
     * @return RuleFound
     */
    private function createRuleFoundInstance(array $customerGroupIds, $country, array $postCodes)
    {
        return new RuleFound(
            CustomerGroupIdList::fromArray($customerGroupIds),
            Country::fromIso2Code($country),
            PostCodeList::fromArray($postCodes)
        );

    }

    /**
     * @param RuleSpecByCountryAndGroupIds $ruleSpec
     * @param mixed[] $records
     * @return RuleFound
     */
    private function combineQueryByCountryAndGroupIdsResultRecords(RuleSpecByCountryAndGroupIds $ruleSpec, array $records)
    {
        $customerGroupIds = [];
        $postCodes = null;
        foreach ($records as $record) {
            $this->validateRUleSpecByCountryAndGroupIdsMatchesResultRecord($ruleSpec, $record);
            $customerGroupIds[] = $record['customer_group_id'];
            $postCodes = $record['post_codes'];
        }
        return $this->createRuleFoundInstance($customerGroupIds, $ruleSpec->getCountryValue(), $postCodes);
    }

    /**
     * @param RuleSpecByCountryAndGroupIds $ruleSpec
     * @param mixed[] $record
     */
    private function validateRUleSpecByCountryAndGroupIdsMatchesResultRecord(RuleSpecByCountryAndGroupIds $ruleSpec, array $record)
    {
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
                'The country "%s" does not match the query country value "%s"', $country, $ruleSpec->getCountryValue()
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

    public function beginTransaction()
    {
        $this->storage->beginTransaction();
    }

    public function commitTransaction()
    {
        $this->storage->commitTransaction();
    }

    public function rollbackTransaction()
    {
        $this->storage->rollbackTransaction();
    }
}
