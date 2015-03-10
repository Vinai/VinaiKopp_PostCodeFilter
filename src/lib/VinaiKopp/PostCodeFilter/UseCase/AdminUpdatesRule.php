<?php


namespace VinaiKopp\PostCodeFilter\UseCase;

use VinaiKopp\PostCodeFilter\Country;
use VinaiKopp\PostCodeFilter\CustomerGroupId;
use VinaiKopp\PostCodeFilter\Exception\RuleDoesNotExistException;
use VinaiKopp\PostCodeFilter\Exception\RuleForGroupAndCountryAlreadyExistsException;
use VinaiKopp\PostCodeFilter\RuleFound;
use VinaiKopp\PostCodeFilter\RuleNotFound;
use VinaiKopp\PostCodeFilter\RuleQuery;
use VinaiKopp\PostCodeFilter\RuleRepository;
use VinaiKopp\PostCodeFilter\RuleToUpdate;

class AdminUpdatesRule
{
    /**
     * @var RuleRepository
     */
    private $ruleRepository;

    public function __construct(RuleRepository $ruleRepository)
    {
        $this->ruleRepository = $ruleRepository;
    }

    public function updateRule(RuleToUpdate $ruleToUpdate)
    {
        $this->validateOldRuleExists($ruleToUpdate);
        $this->validateNewRuleDoesNotConflict($ruleToUpdate);
        $this->ruleRepository->updateRule($ruleToUpdate);
    }

    /**
     * @param RuleToUpdate $ruleToUpdate
     * @throws RuleDoesNotExistException
     */
    private function validateOldRuleExists(RuleToUpdate $ruleToUpdate)
    {
        $ruleQuery = $this->buildRuleQuery($ruleToUpdate->getOldCustomerGroupId(), $ruleToUpdate->getOldCountry());
        $existingRule = $this->ruleRepository->findByGroupAndCountry($ruleQuery);
        if ($existingRule instanceof RuleNotFound) {
            throw $this->buildRuleDoesNotExistException($ruleToUpdate);
        }
    }

    /**
     * @param RuleToUpdate $ruleToUpdate
     * @throws RuleForGroupAndCountryAlreadyExistsException
     */
    private function validateNewRuleDoesNotConflict(RuleToUpdate $ruleToUpdate)
    {
        if ($ruleToUpdate->isGroupOrCountryChanged()) {
            $ruleQuery = $this->buildRuleQuery($ruleToUpdate->getNewCustomerGroupId(), $ruleToUpdate->getNewCountry());
            $existingRule = $this->ruleRepository->findByGroupAndCountry($ruleQuery);
            if ($existingRule instanceof RuleFound) {
                throw $this->buildConflictingRuleAlreadyExistsException($ruleToUpdate);
            }
        }
    }

    private function buildRuleQuery(CustomerGroupId $groupId, Country $country)
    {
        return new RuleQuery($groupId, $country);
    }

    private function buildRuleDoesNotExistException(RuleToUpdate $ruleToUpdate)
    {
        return new RuleDoesNotExistException(sprintf(
            'Unable to update rule: no existing rule found for the old group "%s" and country "%s"',
            $ruleToUpdate->getOldCustomerGroupIdValue(),
            $ruleToUpdate->getOldCountryValue()
        ));
    }

    private function buildConflictingRuleAlreadyExistsException(RuleToUpdate $ruleToUpdate)
    {
        return new RuleForGroupAndCountryAlreadyExistsException(sprintf(
            'Unable to update rule: a rule for the new group "%s" and country "%s" already exists',
            $ruleToUpdate->getNewCustomerGroupIdValue(),
            $ruleToUpdate->getNewCountryValue()
        ));
    }
}
