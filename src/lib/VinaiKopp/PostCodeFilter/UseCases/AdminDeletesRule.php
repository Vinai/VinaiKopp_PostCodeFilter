<?php


namespace VinaiKopp\PostCodeFilter\UseCases;


use VinaiKopp\PostCodeFilter\Command\RuleToDelete;
use VinaiKopp\PostCodeFilter\Command\RuleWriter;
use VinaiKopp\PostCodeFilter\Exceptions\RuleDoesNotExistException;
use VinaiKopp\PostCodeFilter\Query\RuleNotFound;
use VinaiKopp\PostCodeFilter\Query\QueryByCountryAndGroupId;
use VinaiKopp\PostCodeFilter\Query\RuleReader;

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
    
    public function deleteRule(RuleToDelete $ruleToDelete)
    {
        $this->validateRuleExists($ruleToDelete);
        $this->ruleWriter->deleteRule($ruleToDelete);
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
        $ruleQuery = new QueryByCountryAndGroupId($ruleToDelete->getCustomerGroupId(), $ruleToDelete->getCountry());
        $result = $this->ruleReader->findByCountryAndGroupId($ruleQuery);
        if ($result instanceof RuleNotFound) {
            throw $this->buildRuleNotExistsException($ruleToDelete);
        }
    }
}
