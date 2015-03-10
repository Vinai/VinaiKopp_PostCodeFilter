<?php


namespace VinaiKopp\PostCodeFilter\UseCase;


use VinaiKopp\PostCodeFilter\Exception\RuleDoesNotExistException;
use VinaiKopp\PostCodeFilter\RuleNotFound;
use VinaiKopp\PostCodeFilter\RuleQuery;
use VinaiKopp\PostCodeFilter\RuleRepository;
use VinaiKopp\PostCodeFilter\RuleToDelete;

class AdminDeletesRule
{
    /**
     * @var RuleRepository
     */
    private $ruleRepository;

    public function __construct(RuleRepository $ruleRepository)
    {
        $this->ruleRepository = $ruleRepository;
    }
    
    public function deleteRule(RuleToDelete $ruleToDelete)
    {
        $this->validateRuleExists($ruleToDelete);
        $this->ruleRepository->deleteRule($ruleToDelete);
    }

    /**
     * @param RuleToDelete $ruleToDelete
     * @return RuleDoesNotExistException
     */
    private function buildRuleNotExistsException(RuleToDelete $ruleToDelete)
    {
        return new RuleDoesNotExistException(sprintf(
            'No rule found with customer group "%s" and country "%s"',
            $ruleToDelete->getCustomerGroupIdValue(),
            $ruleToDelete->getCountryValue()
        ));
    }

    /**
     * @param RuleToDelete $ruleToDelete
     */
    private function validateRuleExists(RuleToDelete $ruleToDelete)
    {
        $ruleQuery = new RuleQuery($ruleToDelete->getCustomerGroupId(), $ruleToDelete->getCountry());
        $result = $this->ruleRepository->findByGroupAndCountry($ruleQuery);
        if ($result instanceof RuleNotFound) {
            throw $this->buildRuleNotExistsException($ruleToDelete);
        }
    }
}
