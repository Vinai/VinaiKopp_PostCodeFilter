<?php


namespace VinaiKopp\PostCodeFilter\Command;


interface RuleWriter
{
    /**
     * @param RuleToAdd $ruleToAdd
     * @return void
     */
    public function createRule(RuleToAdd $ruleToAdd);

    /**
     * @param RuleToDelete $ruleToDelete
     * @return void
     */
    public function deleteRule(RuleToDelete $ruleToDelete);
}
