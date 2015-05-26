<?php

use VinaiKopp\PostCodeFilter\Rule\Rule;
use VinaiKopp\PostCodeFilter\AdminViewsRuleList;

class VinaiKopp_PostCodeFilter_Model_RuleCollection extends Varien_Data_Collection_Db
{
    const SORT_RESULT_A_LESS_THEN_B = -1;
    const SORT_RESULT_A_MORE_THEN_B = 1;
    
    /**
     * @var AdminViewsRuleList
     */
    private $useCase;

    public function __construct($conn = null)
    {
        parent::__construct($conn ?: null);
    }

    /**
     * @param string $field
     * @param null|string[] $condition
     * @return $this
     * @throws Mage_Core_Exception
     */
    public function addFieldToFilter($field, $condition = null)
    {
        switch (key($condition) . ' ' . $field) {
            case 'like country':
                $filterString = $this->convertLikeZendExprToString(current($condition));
                $this->getUseCase()->setCountryFilter($filterString);
                break;
            case 'eq customer_groups':
                $filterString = current($condition);
                $this->getUseCase()->setCustomerGroupIdFilter($filterString);
                break;
            case 'like post_codes':
                $filterString = $this->convertLikeZendExprToString(current($condition));
                $this->getUseCase()->setPostCodeFilter($filterString);
                break;
            default:
                Mage::throwException(
                    sprintf('Unsupported filter: %s %s', key($condition), $field)
                );
                // @codeCoverageIgnoreStart 
        }
        // @codeCoverageIgnoreEnd
        return $this;
    }

    /**
     * @param string $field
     * @param string $direction
     * @return $this
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        switch ($field) {
            case 'country':
                $this->getUseCase()->sortByCountry($direction);
                break;
            case 'customer_groups':
                $this->getUseCase()->sortByCustomerGroupId($direction);
                break;
            case 'post_codes':
                $this->getUseCase()->sortByPostCode($direction);
                break;
        }
        return $this;
    }

    /**
     * @param string $field
     * @param string $direction
     * @return VinaiKopp_PostCodeFilter_Model_RuleCollection
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        return $this->setOrder($field, $direction);
    }


    public function getSize()
    {
        $this->load();
        return count($this);
    }

    public function load($printQuery = false, $logQuery = false)
    {
        $rules = $this->getPostCodeFilterRules();
        $this->_items = array_map([$this, 'convertRuleToVarienObject'], $rules);
        $this->_setIsLoaded(true);
        return $this;
    }
    
    public function setUseCase(AdminViewsRuleList $useCase)
    {
        $this->useCase = $useCase;
    }

    private function getUseCase()
    {
        if (is_null($this->useCase)) {
            // @codeCoverageIgnoreStart
            /** @var VinaiKopp_PostCodeFilter_Helper_Data $helper */
            $helper = Mage::helper('vinaikopp_postcodefilter');
            $this->useCase = $helper->createAdminViewsRuleListUseCase();
        }
        // @codeCoverageIgnoreEnd
        return $this->useCase;
    }

    /**
     * @return Rule[]
     */
    private function getPostCodeFilterRules()
    {
        return $this->getUseCase()->fetchRules();
    }

    /**
     * @param Rule $rule
     * @return Varien_Object
     */
    private function convertRuleToVarienObject(Rule $rule)
    {
        return new Varien_Object([
            'country' => $rule->getCountryValue(),
            'customer_groups' => $rule->getCustomerGroupIdValues(),
            'post_codes' => $rule->getPostCodeValues()
        ]);
    }
    
    /**
     * @return mixed
     */
    private function convertLikeZendExprToString($quotedLikeExpression)
    {
        return trim($quotedLikeExpression, "'%");
    }
}
