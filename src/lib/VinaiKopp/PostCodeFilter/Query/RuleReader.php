<?php

namespace VinaiKopp\PostCodeFilter\Query;

interface RuleReader
{
    /**
     * @param RuleSpecByCountryAndGroupId $ruleSpec
     * @return RuleResult
     */
    public function findByCountryAndGroupId(RuleSpecByCountryAndGroupId $ruleSpec);

    /**
     * @param RuleSpecByCountryAndGroupIds $ruleSpec
     * @return RuleResult
     */
    public function findByCountryAndGroupIds(RuleSpecByCountryAndGroupIds $ruleSpec);

    /**
     * @return RuleResult[]
     */
    public function findAll();
}
