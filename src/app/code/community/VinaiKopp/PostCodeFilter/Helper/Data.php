<?php


use VinaiKopp\PostCodeFilter\Query\RuleSpecByCountryAndGroupIds;
use VinaiKopp\PostCodeFilter\RuleComponents\Country;
use VinaiKopp\PostCodeFilter\RuleComponents\CustomerGroupIdList;
use VinaiKopp\PostCodeFilter\RuleRepository;
use VinaiKopp\PostCodeFilter\RuleStorage;
use VinaiKopp\PostCodeFilter\UseCases\CustomerChecksPostCode;

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
     * @return RuleSpecByCountryAndGroupIds
     */
    public function createRuleSpecForGroupIdsAndCountry(array $customerGroupIds, $country)
    {
        $this->registerPostCodeFilterAutoloader();
        return new RuleSpecByCountryAndGroupIds(
            Country::fromIso2Code($country),
            CustomerGroupIdList::fromArray(array_map('intval', $customerGroupIds))
        );
    }

    /**
     * @return CustomerChecksPostCode
     */
    public function createCustomerChecksPostCodeUseCase()
    {
        $this->registerPostCodeFilterAutoloader();
        return new CustomerChecksPostCode($this->getRuleReader());
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
