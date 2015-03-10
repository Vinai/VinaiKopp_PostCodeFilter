<?php


namespace VinaiKopp\PostCodeFilter;

class RuleRepository
{
    /**
     * @var RuleStorage
     */
    private $storage;

    public function __construct(RuleStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param RuleQuery $ruleQuery
     * @return Rule
     */
    public function findByGroupAndCountry(RuleQuery $ruleQuery)
    {
        $postCodes = $this->storage->findPostCodesByGroupAndCountry(
            $ruleQuery->getCustomerGroupIdValue(),
            $ruleQuery->getCountryValue()
        );
        if (empty($postCodes)) {
            return new RuleNotFound();
        }
        return new RuleFound($postCodes);
    }

    public function createRule(RuleToAdd $ruleToAdd)
    {
        $this->storage->create($ruleToAdd);
    }

    public function updateRule(RuleToUpdate $ruleToUpdate)
    {
        $this->storage->update($ruleToUpdate);
    }

    public function deleteRule($ruleToDelete)
    {
        $this->storage->delete($ruleToDelete);
    }
}
