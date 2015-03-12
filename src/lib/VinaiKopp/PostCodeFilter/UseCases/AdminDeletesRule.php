<?php


namespace VinaiKopp\PostCodeFilter\UseCases;


use VinaiKopp\PostCodeFilter\Command\RuleToDelete;
use VinaiKopp\PostCodeFilter\Command\RuleWriter;
use VinaiKopp\PostCodeFilter\Exceptions\RuleDoesNotExistException;
use VinaiKopp\PostCodeFilter\Query\QueryByCountryAndGroupIds;
use VinaiKopp\PostCodeFilter\Query\RuleNotFound;
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
     */
    private function validateRuleExists(RuleToDelete $ruleToDelete)
    {
        $ruleQuery = new QueryByCountryAndGroupIds($ruleToDelete->getCountry(), $ruleToDelete->getCustomerGroupIds());
        $result = $this->ruleReader->findByCountryAndGroupIds($ruleQuery);
        if ($result instanceof RuleNotFound) {
            throw $this->makeRuleNotExistsException($ruleToDelete);
        }
    }

    /**
     * @param RuleToDelete $ruleToDelete
     * @return RuleDoesNotExistException
     */
    private function makeRuleNotExistsException(RuleToDelete $ruleToDelete)
    {
        return new RuleDoesNotExistException(sprintf(
            'No rule found with customer groups "%s" and country "%s"',
            implode(', ', $ruleToDelete->getCustomerGroupIdValues()),
            $ruleToDelete->getCountryValue()
        ));
    }
}
