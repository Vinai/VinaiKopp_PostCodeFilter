<?php

use VinaiKopp\PostCodeFilter\Storage\RuleRepository;
use VinaiKopp\PostCodeFilter\Storage\RuleRepositoryWriter;
use VinaiKopp\PostCodeFilter\Storage\RuleStorage;
use VinaiKopp\PostCodeFilter\AdminViewsRuleList;
use VinaiKopp\PostCodeFilter\CustomerSpecifiesShippingAddress;

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
        return new RuleRepositoryWriter($this->storage);
    }

    public function getRuleReader()
    {
        return new RuleRepository($this->storage);
    }

    /**
     * @return CustomerSpecifiesShippingAddress
     */
    public function createCustomerChecksPostCodeUseCase()
    {
        $this->registerPostCodeFilterAutoloader();
        return new CustomerSpecifiesShippingAddress($this->getRuleReader());
    }

    /**
     * @return AdminViewsRuleList
     */
    public function createAdminViewsRuleListUseCase()
    {
        $this->registerPostCodeFilterAutoloader();
        return new AdminViewsRuleList($this->getRuleReader());
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
