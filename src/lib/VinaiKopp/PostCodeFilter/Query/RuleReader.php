<?php


namespace VinaiKopp\PostCodeFilter\Query;


interface RuleReader
{
    /**
     * @param QueryByCountryAndGroupId $ruleQuery
     * @return RuleResult
     */
    public function findByCountryAndGroupId(QueryByCountryAndGroupId $ruleQuery);

    /**
     * @param QueryByCountryAndGroupIds $ruleQuery
     * @return RuleResult
     */
    public function findByCountryAndGroupIds(QueryByCountryAndGroupIds $ruleQuery);

    /**
     * @return RuleResult[]
     */
    public function findAll();
}
