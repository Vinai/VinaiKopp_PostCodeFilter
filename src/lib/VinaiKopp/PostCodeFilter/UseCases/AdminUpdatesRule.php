<?php


namespace VinaiKopp\PostCodeFilter\UseCases;


use VinaiKopp\PostCodeFilter\Command\RuleToAdd;
use VinaiKopp\PostCodeFilter\Command\RuleToDelete;
use VinaiKopp\PostCodeFilter\Command\RuleToUpdate;
use VinaiKopp\PostCodeFilter\Command\RuleWriter;
use VinaiKopp\PostCodeFilter\Exceptions\RuleDoesNotExistException;
use VinaiKopp\PostCodeFilter\Query\RuleSpecByCountryAndGroupIds;
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
     * @param string $oldIso2Country
     * @param int[] $oldCustomerGroupIds
     * @param string $newIso2Country
     * @param int[] $newCustomerGroupIds
     * @param string[] $newPostCodes
     */
    public function updateRuleFromScalars(
        $oldIso2Country,
        array $oldCustomerGroupIds,
        $newIso2Country,
        array $newCustomerGroupIds,
        array $newPostCodes
    ) {
        $this->updateRule(RuleToUpdate::createFromScalars(
            $oldIso2Country,
            $oldCustomerGroupIds,
            $newIso2Country,
            $newCustomerGroupIds,
            $newPostCodes
        ));
    }

    /**
     * @param RuleToUpdate $ruleToUpdate
     * @throws RuleDoesNotExistException
     */
    private function validateOldRuleExists(RuleToUpdate $ruleToUpdate)
    {
        $result = $this->fetchExistingRule($ruleToUpdate);
        if ($result instanceof RuleNotFound) {
            throw $this->createRuleDoesNotExistException($ruleToUpdate);
        }
    }

    /**
     * @param RuleToUpdate $ruleToUpdate
     * @return RuleResult
     */
    private function fetchExistingRule(RuleToUpdate $ruleToUpdate)
    {
        $ruleSpec = $this->createRuleSpecByCountryAndGroupIds(
            $ruleToUpdate->getOldCountry(),
            $ruleToUpdate->getOldCustomerGroupIds()
        );
        return $this->ruleReader->findByCountryAndGroupIds($ruleSpec);
    }

    /**
     * @param RuleToUpdate $ruleToUpdate
     * @return RuleDoesNotExistException
     */
    private function createRuleDoesNotExistException(RuleToUpdate $ruleToUpdate)
    {
        return new RuleDoesNotExistException(sprintf(
            'Update failure: there is no rule for the country "%s" and the customer group ID(s) "%s"',
            $ruleToUpdate->getOldCountryValue(),
            implode(', ', $ruleToUpdate->getOldCustomerGroupIdValues())
        ));
    }

    private function createRuleSpecByCountryAndGroupIds(Country $country, CustomerGroupIdList $customerGroupIds)
    {
        return new RuleSpecByCountryAndGroupIds($country, $customerGroupIds);
    }

    private function deleteOldRule(RuleToUpdate $ruleToUpdate)
    {
        $ruleToDelete = $this->createRuleToDelete(
            $ruleToUpdate->getOldCountry(),
            $ruleToUpdate->getOldCustomerGroupIds()
        );
        $this->ruleWriter->deleteRule($ruleToDelete);
    }

    private function createRuleToDelete(Country $country, CustomerGroupIdList $customerGroupIds)
    {
        return new RuleToDelete($customerGroupIds, $country);
    }

    private function insertNewRule(RuleToUpdate $ruleToUpdate)
    {
        $ruleToAdd = $this->createRuleToAdd(
            $ruleToUpdate->getNewCountry(),
            $ruleToUpdate->getNewCustomerGroupIds(),
            $ruleToUpdate->getNewPostCodes()
        );
        $this->ruleWriter->createRule($ruleToAdd);
    }

    private function createRuleToAdd(Country $country, CustomerGroupIdList $customerGroupIds, PostCodeList $postCodes)
    {
        return new RuleToAdd($customerGroupIds, $country, $postCodes);
    }
}
