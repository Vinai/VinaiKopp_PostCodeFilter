<?php

namespace VinaiKopp\PostCodeFilter\UseCases;

use VinaiKopp\PostCodeFilter\Command\RuleToAdd;
use VinaiKopp\PostCodeFilter\Command\RuleToDelete;
use VinaiKopp\PostCodeFilter\Command\RuleWriter;
use VinaiKopp\PostCodeFilter\Exceptions\RuleDoesNotExistException;
use VinaiKopp\PostCodeFilter\Query\RuleSpecByCountryAndGroupIds;
use VinaiKopp\PostCodeFilter\Query\RuleNotFound;
use VinaiKopp\PostCodeFilter\Query\RuleReader;
use VinaiKopp\PostCodeFilter\Query\RuleResult;

class AdminUpdatesRule
{
    /**
     * @var RuleWriter
     */
    private $ruleWriter;

    /**
     * @var RuleReader
     */
    private $ruleReader;

    public function __construct(RuleWriter $ruleWriter, RuleReader $ruleReader)
    {
        $this->ruleWriter = $ruleWriter;
        $this->ruleReader = $ruleReader;
    }

    /**
     * @param RuleToDelete $ruleToDelete
     * @param RuleToAdd $ruleToAdd
     * @throws \Exception
     */
    public function updateRule(RuleToDelete $ruleToDelete, RuleToAdd $ruleToAdd)
    {
        try {
            $this->ruleWriter->beginTransaction();
            $this->validateOldRuleExists($ruleToDelete);
            $this->ruleWriter->deleteRule($ruleToDelete);
            $this->ruleWriter->createRule($ruleToAdd);
            $this->ruleWriter->commitTransaction();
        } catch (\Exception $e) {
            $this->ruleWriter->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * @param string $oldIso2Country
     * @param int[] $oldCustomerGroupIds
     * @param string $newIso2Country
     * @param int[] $newCustomerGroupIds
     * @param string[] $newPostCodes
     */
    public function updateRuleFromScalars(
        $oldIso2Country,
        array $oldCustomerGroupIds,
        $newIso2Country,
        array $newCustomerGroupIds,
        array $newPostCodes
    ) {
        $this->updateRule(
            RuleToDelete::createFromScalars($oldCustomerGroupIds, $oldIso2Country),
            RuleToAdd::createFromScalars($newCustomerGroupIds, $newIso2Country, $newPostCodes)
        );
    }

    /**
     * @param RuleToDelete $ruleToDelete
     * @throws RuleDoesNotExistException
     */
    private function validateOldRuleExists(RuleToDelete $ruleToDelete)
    {
        $ruleSpec = $this->createRuleSpecByCountryAndGroupIds($ruleToDelete);
        $result = $this->fetchExistingRule($ruleSpec);
        if ($result instanceof RuleNotFound) {
            throw $this->createRuleDoesNotExistException($ruleToDelete);
        }
    }

    /**
     * @param RuleToDelete $ruleToDelete
     * @return RuleSpecByCountryAndGroupIds
     */
    private function createRuleSpecByCountryAndGroupIds(RuleToDelete $ruleToDelete)
    {
        return new RuleSpecByCountryAndGroupIds(
            $ruleToDelete->getCountry(),
            $ruleToDelete->getCustomerGroupIds()
        );
    }

    /**
     * @param RuleSpecByCountryAndGroupIds $ruleSpec
     * @return RuleResult
     */
    private function fetchExistingRule(RuleSpecByCountryAndGroupIds $ruleSpec)
    {
        return $this->ruleReader->findByCountryAndGroupIds($ruleSpec);
    }

    /**
     * @param RuleToDelete $ruleToDelete
     * @return RuleDoesNotExistException
     */
    private function createRuleDoesNotExistException(RuleToDelete $ruleToDelete)
    {
        return new RuleDoesNotExistException(sprintf(
            'Update failure: there is no rule for the country "%s" and the customer group ID(s) "%s"',
            $ruleToDelete->getCountryValue(),
            implode(', ', $ruleToDelete->getCustomerGroupIdValues())
        ));
    }
}
