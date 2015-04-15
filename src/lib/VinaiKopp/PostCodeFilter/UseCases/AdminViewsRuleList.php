<?php

namespace VinaiKopp\PostCodeFilter\UseCases;

use VinaiKopp\PostCodeFilter\Query\RuleReader;
use VinaiKopp\PostCodeFilter\Query\Rule;

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
     * @return Rule[]
     */
    public function fetchAllRules()
    {
        return $this->ruleReader->findAll();
    }
}
