<?php


namespace VinaiKopp\PostCodeFilter;

use VinaiKopp\PostCodeFilter\Command\RuleToAdd;
use VinaiKopp\PostCodeFilter\Command\RuleToDelete;
use VinaiKopp\PostCodeFilter\Command\RuleWriter;
use VinaiKopp\PostCodeFilter\Exceptions\NonMatchingRecordInResultException;
use VinaiKopp\PostCodeFilter\Query\RuleFound;
use VinaiKopp\PostCodeFilter\Query\RuleNotFound;
use VinaiKopp\PostCodeFilter\Query\QueryByCountryAndGroupId;
use VinaiKopp\PostCodeFilter\Query\QueryByCountryAndGroupIds;
use VinaiKopp\PostCodeFilter\Query\RuleReader;
use VinaiKopp\PostCodeFilter\Query\RuleResult;
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
     * @param QueryByCountryAndGroupId $ruleQuery
     * @return RuleResult
     */
    public function findByCountryAndGroupId(QueryByCountryAndGroupId $ruleQuery)
    {
        $postCodes = $this->storage->findPostCodesByCountryAndGroupId(
            $ruleQuery->getCountryValue(), $ruleQuery->getCustomerGroupIdValue()
        );
        if (empty($postCodes)) {
            return new RuleNotFound($ruleQuery->getCountry());
        }
        return $this->makeRuleFound([$ruleQuery->getCustomerGroupIdValue()], $ruleQuery->getCountryValue(), $postCodes);
    }

    /**
     * @param QueryByCountryAndGroupIds $ruleQuery
     * @return RuleResult
     */
    public function findByCountryAndGroupIds(QueryByCountryAndGroupIds $ruleQuery)
    {
        $records = $this->storage->findRulesByCountryAndGroupIds(
            $ruleQuery->getCountryValue(), $ruleQuery->getCustomerGroupIdValues()
        );
        if (empty($records)) {
            return new RuleNotFound($ruleQuery->getCountry());
        }
        return $this->combineQueryByCountryAndGroupIdsResultRecords($ruleQuery, $records);
    }

    /**
     * @return RuleResult[]
     */
    public function findAll()
    {
        return $this->createRulesFromRecords($this->storage->findAllRules());
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
    private function createRulesFromRecords(array $records)
    {
        $combinedRecords = $this->mergeMatchingCountryAndPostcodes($records);
        return array_map(function (array $record) {
            return $this->makeRuleFound($record['customer_group_ids'], $record['country'], $record['post_codes']);
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
    private function makeRuleFound(array $customerGroupIds, $country, array $postCodes)
    {
        return new RuleFound(
            CustomerGroupIdList::fromArray($customerGroupIds),
            Country::fromIso2Code($country),
            PostCodeList::fromArray($postCodes)
        );

    }

    /**
     * @param QueryByCountryAndGroupIds $ruleQuery
     * @param mixed[] $records
     * @return RuleFound
     */
    private function combineQueryByCountryAndGroupIdsResultRecords(QueryByCountryAndGroupIds $ruleQuery, array $records)
    {
        $customerGroupIds = [];
        $postCodes = null;
        foreach ($records as $record) {
            $this->validateQueryByCountryAndGroupIdsResultRecord($ruleQuery, $record);
            $customerGroupIds[] = $record['customer_group_id'];
            $postCodes = $record['post_codes'];
        }
        return $this->makeRuleFound($customerGroupIds, $ruleQuery->getCountryValue(), $postCodes);
    }

    /**
     * @param QueryByCountryAndGroupIds $ruleQuery
     * @param mixed[] $record
     */
    private function validateQueryByCountryAndGroupIdsResultRecord(QueryByCountryAndGroupIds $ruleQuery, array $record)
    {
        $this->validateCountryMatchesQuery($record['country'], $ruleQuery);
        $this->validateCustomerGroupIdMatchesQuery($record['customer_group_id'], $ruleQuery);
    }

    /**
     * @param string $country
     * @param QueryByCountryAndGroupIds $ruleQuery
     * @throws NonMatchingRecordInResultException
     */
    private function validateCountryMatchesQuery($country, QueryByCountryAndGroupIds $ruleQuery)
    {
        if ($country != $ruleQuery->getCountryValue()) {
            throw new NonMatchingRecordInResultException(sprintf(
                'The country "%s" does not match the query country value "%s"', $country, $ruleQuery->getCountryValue()
            ));
        }
    }

    /**
     * @param int $customerGroupId
     * @param QueryByCountryAndGroupIds $ruleQuery
     * @throws NonMatchingRecordInResultException
     */
    private function validateCustomerGroupIdMatchesQuery($customerGroupId, QueryByCountryAndGroupIds $ruleQuery)
    {
        if (!in_array($customerGroupId, $ruleQuery->getCustomerGroupIdValues())) {
            throw new NonMatchingRecordInResultException(sprintf(
                'The customer group ID "%s" does not match the query customer group ID values "%s"',
                $customerGroupId,
                implode(', ', $ruleQuery->getCustomerGroupIdValues())
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
