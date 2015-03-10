<?php


namespace VinaiKopp\PostCodeFilter\UseCase;


use VinaiKopp\PostCodeFilter\Exception\RuleAlreadyExistsException;
use VinaiKopp\PostCodeFilter\RuleFound;
use VinaiKopp\PostCodeFilter\RuleToAdd;
use VinaiKopp\PostCodeFilter\RuleQuery;
use VinaiKopp\PostCodeFilter\RuleRepository;

class AdminAddsRule
{
    /**
     * @var RuleRepository
     */
    private $ruleRepository;

    public function __construct(RuleRepository $ruleRepository)
    {
        $this->ruleRepository = $ruleRepository;
    }

    /**
     * @param RuleToAdd $ruleToAdd
     */
    public function addRule(RuleToAdd $ruleToAdd)
    {
        $this->validateNoConflictingRuleExists($ruleToAdd);
        $this->ruleRepository->createRule($ruleToAdd);
    }

    /**
     * @param RuleToAdd $ruleToAdd
     * @return RuleQuery
     */
    private function buildRuleQuery(RuleToAdd $ruleToAdd)
    {
        return new RuleQuery($ruleToAdd->getCustomerGroupId(), $ruleToAdd->getCountry());
    }

    /**
     * @param RuleToAdd $ruleToAdd
     */
    private function validateNoConflictingRuleExists(RuleToAdd $ruleToAdd)
    {
        $ruleQuery = $this->buildRuleQuery($ruleToAdd);
        $result = $this->ruleRepository->findByGroupAndCountry($ruleQuery);
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
