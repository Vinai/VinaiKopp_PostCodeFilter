<?php


use VinaiKopp\PostCodeFilter\Command\RuleToAdd;
use VinaiKopp\PostCodeFilter\Command\RuleToDelete;
use VinaiKopp\PostCodeFilter\Command\RuleToUpdate;
use VinaiKopp\PostCodeFilter\Query\QueryByCountryAndGroupIds;
use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\RuleComponents\PostCodeList;
use VinaiKopp\PostCodeFilter\RuleRepository;
use VinaiKopp\PostCodeFilter\RuleStorage;

class VinaiKopp_PostCodeFilter_Helper_Data extends Mage_Core_Helper_Abstract
{
    private $autoloaderRegistered = false;
    
    /**
     * @var RuleStorage
     */
    private $storage;

    public function __construct(RuleStorage $storage = null)
    {
        if (is_null($storage)) {
            $this->registerPostCodeFilterAutoloader();
            $storage = Mage::getResourceModel('vinaikopp_postcodefilter/ruleStorage');
        }
        $this->storage = $storage;
    }

    public function getRuleWriter()
    {
        return new RuleRepository($this->storage);
    }

    public function getRuleReader()
    {
        return new RuleRepository($this->storage);
    }

    /**
     * @param int[] $customerGroupIds
     * @param string $country
     * @param string[]|int[] $postCodes
     * @return RuleToAdd
     */
    public function createRuleToAdd(array $customerGroupIds, $country, array $postCodes)
    {
        array_walk($customerGroupIds, [$this, 'convertToInteger']);
        $this->registerPostCodeFilterAutoloader();
        return new RuleToAdd(
            CustomerGroupIdList::fromArray($customerGroupIds),
            Country::fromIso2Code($country),
            PostCodeList::fromArray($postCodes)
        );
    }

    /**
     * @param int[] $customerGroupIds
     * @param string $country
     * @return RuleToDelete
     */
    public function createRuleToDelete(array $customerGroupIds, $country)
    {
        array_walk($customerGroupIds, [$this, 'convertToInteger']);
        $this->registerPostCodeFilterAutoloader();
        return new RuleToDelete(
            CustomerGroupIdList::fromArray($customerGroupIds),
            Country::fromIso2Code($country)
        );
    }

    /**
     * @param int[] $customerGroupIds
     * @param string $country
     * @return QueryByCountryAndGroupIds
     */
    public function createRuleQueryForGroupIdsAndCountry(array $customerGroupIds, $country)
    {
        array_walk($customerGroupIds, [$this, 'convertToInteger']);
        $this->registerPostCodeFilterAutoloader();
        return new QueryByCountryAndGroupIds(
            Country::fromIso2Code($country),
            CustomerGroupIdList::fromArray($customerGroupIds)
        );
    }

    public function createRuleToUpdate(
        $oldCountry,
        $oldCustomerGroupIds,
        $newCountry,
        $newCustomerGroupIds,
        $newPostCodes
    ) {
        array_walk($oldCustomerGroupIds, [$this, 'convertToInteger']);
        array_walk($newCustomerGroupIds, [$this, 'convertToInteger']);
        $this->registerPostCodeFilterAutoloader();
        return new RuleToUpdate(
            Country::fromIso2Code($oldCountry),
            CustomerGroupIdList::fromArray($oldCustomerGroupIds),
            Country::fromIso2Code($newCountry),
            CustomerGroupIdList::fromArray($newCustomerGroupIds),
            PostCodeList::fromArray($newPostCodes)
        );
    }

    /**
     * @param mixed $value
     * @return int
     */
    private function convertToInteger(&$value) {
        $value = (int) $value;
    }

    private function registerPostCodeFilterAutoloader()
    {
        if ($this->autoloaderRegistered) {
            return;
        }
        $this->autoloaderRegistered = true;
        spl_autoload_register(function ($class) {
            $prefix = 'VinaiKopp\\PostCodeFilter\\';
            if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
                return;
            }

            $classFile = str_replace('\\', '/', $class) . '.php';
            $file = BP . '/lib/' . $classFile;
            if (file_exists($file)) {
                require $file;
            }
        }, false, true);
    }
}
