<?php


namespace VinaiKopp\PostCodeFilter\UseCases;



use VinaiKopp\PostCodeFilter\Query\RuleReader;

class AdminListsRules
{
    /**
     * @var RuleReader
     */
    private $ruleReader;

    public function __construct(RuleReader $ruleReader)
    {
        $this->ruleReader = $ruleReader;
    }
    
    public function fetchAll()
    {
        return $this->ruleReader->findAll();
    }
}
