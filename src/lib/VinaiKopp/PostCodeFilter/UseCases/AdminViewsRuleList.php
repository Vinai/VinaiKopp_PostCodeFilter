<?php


namespace VinaiKopp\PostCodeFilter\UseCases;



use VinaiKopp\PostCodeFilter\Query\RuleReader;

class AdminViewsRuleList
{
    /**
     * @var RuleReader
     */
    private $ruleReader;

    public function __construct(RuleReader $ruleReader)
    {
        $this->ruleReader = $ruleReader;
    }
    
    public function fetchAllRules()
    {
        return $this->ruleReader->findAll();
    }
}
