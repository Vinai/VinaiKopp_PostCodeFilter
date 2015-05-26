<?php

namespace VinaiKopp\PostCodeFilter\Storage\ReadModel;

use VinaiKopp\PostCodeFilter\Rule\Rule;

interface RuleReader
{
    /**
     * @param RuleSpecByCountryAndGroupId $ruleSpec
     * @return Rule
     */
    public function findByCountryAndGroupId(RuleSpecByCountryAndGroupId $ruleSpec);

    /**
     * @param RuleSpecByCountryAndGroupIds $ruleSpec
     * @return Rule
     */
    public function findByCountryAndGroupIds(RuleSpecByCountryAndGroupIds $ruleSpec);

    /**
     * @return Rule[]
     */
    public function findAll();
}
