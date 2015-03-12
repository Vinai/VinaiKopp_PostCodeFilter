<?php


namespace VinaiKopp\PostCodeFilter\UseCases;


use VinaiKopp\PostCodeFilter\Command\RuleToAdd;
use VinaiKopp\PostCodeFilter\Command\RuleWriter;
use VinaiKopp\PostCodeFilter\Exceptions\RuleAlreadyExistsException;
use VinaiKopp\PostCodeFilter\Query\RuleFound;
use VinaiKopp\PostCodeFilter\Query\QueryByCountryAndGroupId;
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
     */
    public function addRule(RuleToAdd $ruleToAdd)
    {
        $this->validateNoConflictingRuleExists($ruleToAdd);
        $this->ruleWriter->createRule($ruleToAdd);
    }

    /**
     * @param RuleToAdd $ruleToAdd
     * @return QueryByCountryAndGroupId
     */
    private function buildRuleQuery(RuleToAdd $ruleToAdd)
    {
        return new QueryByCountryAndGroupId($ruleToAdd->getCustomerGroupId(), $ruleToAdd->getCountry());
    }

    /**
     * @param RuleToAdd $ruleToAdd
     */
    private function validateNoConflictingRuleExists(RuleToAdd $ruleToAdd)
    {
        $ruleQuery = $this->buildRuleQuery($ruleToAdd);
        $result = $this->ruleReader->findByCountryAndGroupId($ruleQuery);
        if ($result instanceof RuleFound) {
            throw $this->createRuleExistsException($ruleToAdd);
        }
    }

    /**
     * @param RuleToAdd $ruleToAdd
     * @return RuleAlreadyExistsException
     */
    private function createRuleExistsException(RuleToAdd $ruleToAdd)
    {
        return new RuleAlreadyExistsException(sprintf(
            'A rule for customer group "%s" and country "%s" already exists',
            $ruleToAdd->getCustomerGroupIdValue(),
            $ruleToAdd->getCountryValue()
        ));
    }
}
