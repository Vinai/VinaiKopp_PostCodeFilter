<?php

namespace VinaiKopp\PostCodeFilter\Storage\WriteModel;

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

    /**
     * @return void
     */
    public function beginTransaction();

    /**
     * @return void
     */
    public function commitTransaction();

    /**
     * @return void
     */
    public function rollbackTransaction();
}
