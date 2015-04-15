<?php

namespace VinaiKopp\PostCodeFilter\UseCases;

use VinaiKopp\PostCodeFilter\Query\RuleReader;
use VinaiKopp\PostCodeFilter\Query\RuleResult;

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

    /**
     * @return RuleResult[]
     */
    public function fetchAllRules()
    {
        return $this->ruleReader->findAll();
    }
}
