<?php


namespace VinaiKopp\PostCodeFilter\UseCases;


use VinaiKopp\PostCodeFilter\Command\RuleToAdd;
use VinaiKopp\PostCodeFilter\Command\RuleWriter;
use VinaiKopp\PostCodeFilter\Exceptions\RuleForGroupAndCountryAlreadyExistsException;
use VinaiKopp\PostCodeFilter\Query\RuleSpecByCountryAndGroupIds;
use VinaiKopp\PostCodeFilter\Query\RuleFound;
use VinaiKopp\PostCodeFilter\Query\RuleReader;

class AdminAddsRule
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
     * @param RuleToAdd $ruleToAdd
     * @throws \Exception
     */
    public function addRule(RuleToAdd $ruleToAdd)
    {
        try {
            $this->ruleWriter->beginTransaction();
            $this->validateNoConflictingRuleExists($ruleToAdd);
            $this->ruleWriter->createRule($ruleToAdd);
            $this->ruleWriter->commitTransaction();
        } catch (\Exception $e) {
            $this->ruleWriter->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * @param int[] $customerGroupIds
     * @param string $iso2Country
     * @param string[] $postCodes
     */
    public function addRuleFromScalars(array $customerGroupIds, $iso2Country, array $postCodes)
    {
        $this->addRule(RuleToAdd::createFromScalars($customerGroupIds, $iso2Country, $postCodes));
    }

    /**
     * @param RuleToAdd $ruleToAdd
     */
    private function validateNoConflictingRuleExists(RuleToAdd $ruleToAdd)
    {
        $ruleSpec = $this->createRuleSpec($ruleToAdd);
        $result = $this->ruleReader->findByCountryAndGroupIds($ruleSpec);
        if ($result instanceof RuleFound) {
            throw $this->createRuleExistsException($result, $ruleToAdd);
        }
    }

    /**
     * @param RuleToAdd $ruleToAdd
     * @return RuleSpecByCountryAndGroupIds
     */
    private function createRuleSpec(RuleToAdd $ruleToAdd)
    {
        return new RuleSpecByCountryAndGroupIds($ruleToAdd->getCountry(), $ruleToAdd->getCustomerGroupIds());
    }

    /**
     * @param RuleFound $existingRule
     * @param RuleToAdd $ruleToAdd
     * @return RuleForGroupAndCountryAlreadyExistsException
     */
    private function createRuleExistsException(RuleFound $existingRule, RuleToAdd $ruleToAdd)
    {
        return new RuleForGroupAndCountryAlreadyExistsException(sprintf(
            'A rule for customer group(s) "%s" and country "%s" already exists',
            implode(', ', $existingRule->getCustomerGroupIdValues()),
            $ruleToAdd->getCountryValue()
        ));
    }
}
