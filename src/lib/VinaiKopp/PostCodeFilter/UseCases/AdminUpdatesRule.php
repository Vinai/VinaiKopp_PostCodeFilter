<?php


namespace VinaiKopp\PostCodeFilter\UseCases;


use VinaiKopp\PostCodeFilter\Command\RuleToAdd;
use VinaiKopp\PostCodeFilter\Command\RuleToDelete;
use VinaiKopp\PostCodeFilter\Command\RuleToUpdate;
use VinaiKopp\PostCodeFilter\Command\RuleWriter;
use VinaiKopp\PostCodeFilter\Exceptions\RuleDoesNotExistException;
use VinaiKopp\PostCodeFilter\Query\QueryByCountryAndGroupIds;
use VinaiKopp\PostCodeFilter\Query\RuleNotFound;
use VinaiKopp\PostCodeFilter\Query\RuleReader;
use VinaiKopp\PostCodeFilter\Query\RuleResult;
use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\RuleComponents\PostCodeList;

class AdminUpdatesRule
{
    /**
     * @var RuleWriter
     */
    private $ruleWriter;

    /**
     * @var RuleReader
     */
    private $ruleReader;

    public function __construct(RuleWriter $ruleWriter, RuleReader $ruleReader)
    {
        $this->ruleWriter = $ruleWriter;
        $this->ruleReader = $ruleReader;
    }

    /**
     * @param RuleToUpdate $ruleToUpdate
     * @throws \Exception
     */
    public function updateRule(RuleToUpdate $ruleToUpdate)
    {
        try {
            $this->ruleWriter->beginTransaction();
            $this->validateOldRuleExists($ruleToUpdate);
            $this->deleteOldRule($ruleToUpdate);
            $this->insertNewRule($ruleToUpdate);
            $this->ruleWriter->commitTransaction();
        } catch (\Exception $e) {
            $this->ruleWriter->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * @param RuleToUpdate $ruleToUpdate
     * @throws RuleDoesNotExistException
     */
    private function validateOldRuleExists(RuleToUpdate $ruleToUpdate)
    {
        $result = $this->fetchExistingRule($ruleToUpdate);
        if ($result instanceof RuleNotFound) {
            throw $this->makeRuleDoesNotExistException($ruleToUpdate);
        }
    }

    /**
     * @param RuleToUpdate $ruleToUpdate
     * @return RuleResult
     */
    private function fetchExistingRule(RuleToUpdate $ruleToUpdate)
    {
        $query = $this->makeQueryByCountryAndGroupIds(
            $ruleToUpdate->getOldCountry(),
            $ruleToUpdate->getOldCustomerGroupIds()
        );
        return $this->ruleReader->findByCountryAndGroupIds($query);
    }

    /**
     * @param RuleToUpdate $ruleToUpdate
     * @return RuleDoesNotExistException
     */
    private function makeRuleDoesNotExistException(RuleToUpdate $ruleToUpdate)
    {
        return new RuleDoesNotExistException(sprintf(
            'Update failure: there is no rule for the country "%s" and the customer group ID(s) "%s"',
            $ruleToUpdate->getOldCountryValue(),
            implode(', ', $ruleToUpdate->getOldCustomerGroupIdValues())
        ));
    }

    private function makeQueryByCountryAndGroupIds(Country $country, CustomerGroupIdList $customerGroupIds)
    {
        return new QueryByCountryAndGroupIds($country, $customerGroupIds);
    }

    private function deleteOldRule(RuleToUpdate $ruleToUpdate)
    {
        $ruleToDelete = $this->makeRuleToDelete(
            $ruleToUpdate->getOldCountry(),
            $ruleToUpdate->getOldCustomerGroupIds()
        );
        $this->ruleWriter->deleteRule($ruleToDelete);
    }

    private function makeRuleToDelete(Country $country, CustomerGroupIdList $customerGroupIds)
    {
        return new RuleToDelete($customerGroupIds, $country);
    }

    private function insertNewRule(RuleToUpdate $ruleToUpdate)
    {
        $ruleToAdd = $this->makeRuleToAdd(
            $ruleToUpdate->getNewCountry(),
            $ruleToUpdate->getNewCustomerGroupIds(),
            $ruleToUpdate->getNewPostCodes()
        );
        $this->ruleWriter->createRule($ruleToAdd);
    }

    private function makeRuleToAdd(Country $country, CustomerGroupIdList $customerGroupIds, PostCodeList $postCodes)
    {
        return new RuleToAdd($customerGroupIds, $country, $postCodes);
    }
}
