<?php

namespace VinaiKopp\PostCodeFilter\Storage;

use VinaiKopp\PostCodeFilter\Storage\WriteModel\RuleToAdd;
use VinaiKopp\PostCodeFilter\Storage\WriteModel\RuleToDelete;
use VinaiKopp\PostCodeFilter\Storage\WriteModel\RuleWriter;

class RuleRepositoryWriter implements RuleWriter
{
    /**
     * @var RuleStorage
     */
    private $storage;

    public function __construct(RuleStorage $storage)
    {
        $this->storage = $storage;
    }

    public function createRule(RuleToAdd $ruleToAdd)
    {
        array_map(function ($customerGroupId) use ($ruleToAdd) {
            $this->storage->create(
                $ruleToAdd->getCountryValue(),
                $customerGroupId,
                $ruleToAdd->getPostCodeValues()
            );
        }, $ruleToAdd->getCustomerGroupIdValues());
    }

    public function deleteRule(RuleToDelete $ruleToDelete)
    {
        array_map(function ($customerGroupId) use ($ruleToDelete) {
            $this->storage->delete($ruleToDelete->getCountryValue(), $customerGroupId);
        }, $ruleToDelete->getCustomerGroupIdValues());
    }

    public function beginTransaction()
    {
        $this->storage->beginTransaction();
    }

    public function commitTransaction()
    {
        $this->storage->commitTransaction();
    }

    public function rollbackTransaction()
    {
        $this->storage->rollbackTransaction();
    }
}
