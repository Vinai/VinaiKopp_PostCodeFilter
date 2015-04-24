<?php

namespace VinaiKopp\PostCodeFilter\UseCases;

use VinaiKopp\PostCodeFilter\WriteModel\RuleToDelete;
use VinaiKopp\PostCodeFilter\WriteModel\RuleWriter;
use VinaiKopp\PostCodeFilter\Exceptions\RuleDoesNotExistException;
use VinaiKopp\PostCodeFilter\ReadModel\RuleSpecByCountryAndGroupIds;
use VinaiKopp\PostCodeFilter\ReadModel\RuleNotFound;
use VinaiKopp\PostCodeFilter\ReadModel\RuleReader;

class AdminDeletesRule
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
     * @throws \Exception
     */
    public function deleteRule(RuleToDelete $ruleToDelete)
    {
        try {
            $this->ruleWriter->beginTransaction();
            $this->validateRuleExists($ruleToDelete);
            $this->ruleWriter->deleteRule($ruleToDelete);
            $this->ruleWriter->commitTransaction();
        } catch (\Exception $e) {
            $this->ruleWriter->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * @param int[] $customerGroupIds
     * @param string $iso2country
     */
    public function deleteRuleFromScalars(array $customerGroupIds, $iso2country)
    {
        $this->deleteRule(RuleToDelete::createFromScalars($customerGroupIds, $iso2country));
    }

    /**
     * @param RuleToDelete $ruleToDelete
     */
    private function validateRuleExists(RuleToDelete $ruleToDelete)
    {
        $ruleSpec = new RuleSpecByCountryAndGroupIds($ruleToDelete->getCountry(), $ruleToDelete->getCustomerGroupIds());
        $result = $this->ruleReader->findByCountryAndGroupIds($ruleSpec);
        if ($result instanceof RuleNotFound) {
            throw $this->createRuleNotExistsException($ruleToDelete);
        }
    }

    /**
     * @param RuleToDelete $ruleToDelete
     * @return RuleDoesNotExistException
     */
    private function createRuleNotExistsException(RuleToDelete $ruleToDelete)
    {
        return new RuleDoesNotExistException(sprintf(
            'No rule found with customer groups "%s" and country "%s"',
            implode(', ', $ruleToDelete->getCustomerGroupIdValues()),
            $ruleToDelete->getCountryValue()
        ));
    }
}
