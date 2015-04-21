<?php

use VinaiKopp\PostCodeFilter\Query\Rule;
use VinaiKopp\PostCodeFilter\UseCases\AdminViewsRuleList;

class VinaiKopp_PostCodeFilter_Model_RuleCollection extends Varien_Data_Collection_Db
{
    const SORT_RESULT_A_LESS_THEN_B = -1;
    const SORT_RESULT_A_MORE_THEN_B = 1;
    
    private $filters = [];

    /**
     * @var AdminViewsRuleList
     */
    private $useCase;

    public function __construct($conn = null)
    {
        parent::__construct($conn ?: null);
    }

    public function addFieldToFilter($field, $condition = null)
    {
        $this->filters[] = [$field, $condition];
        return $this;
    }

    public function getSize()
    {
        $this->load();
        return count($this);
    }

    public function load($printQuery = false, $logQuery = false)
    {
        $rules = $this->getAllPostCodeFilterRules();
        $this->_items = array_filter(
            array_map([$this, 'convertRuleToVarienObject'], $rules),
            [$this, 'applyFilters']
        );
        if ($this->_orders) {
            uasort($this->_items, [$this, 'applySorting']);
        }
        $this->_setIsLoaded(true);
        return $this;
    }

    private function applySorting(Varien_Object $a, Varien_Object $b)
    {
        $result = 0;
        foreach ($this->_orders as $field => $direction) {
            $valueA = $a->getData($field);
            $valueB = $b->getData($field);
            if (is_string($valueA) && is_string($valueB)) {
                $result = $this->compareStringValues($valueA, $valueB, $direction);
            } elseif (is_array($valueA) && is_array($valueB)) {
                $result = $this->compareArrays($valueA, $valueB, $direction);
            }
            if (0 !== $result) {
                break;
            }
        }
        return $result;
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
            $this->useCase = new AdminViewsRuleList($helper->getRuleReader());
        }
        // @codeCoverageIgnoreEnd
        return $this->useCase;
    }

    /**
     * @return Rule[]
     */
    private function getAllPostCodeFilterRules()
    {
        return $this->getUseCase()->fetchAllRules();
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


    private function applyFilters(Varien_Object $object)
    {
        return array_reduce($this->filters, function ($acc, array $filter) use ($object) {
            list ($field, $condition) = $filter;
            return $acc && $this->isMatchingFilter($object->getData($field), $condition);
        }, true);
    }

    private function isMatchingFilter($value, array $condition)
    {
        $operator = key($condition);
        if ('eq' == $operator) {
            $filterValue = current($condition);
            return is_array($value) ?
                in_array($filterValue, $value) :
                $filterValue == $value;
        }
        if ('like' == $operator) {
            $pattern = $this->convertLikeZendExprToRegex(current($condition));
            return preg_match($pattern, $value);
        }
        Mage::throwException(sprintf('Filter operator "%s" not implemented', $operator));
    }

    /**
     * @return mixed
     */
    private function convertLikeZendExprToRegex($quotedLikeExpression)
    {
        $compareValue = trim($quotedLikeExpression, "'");
        $pattern = str_replace(['%', '/'], ['.*', '\\/'], $compareValue);
        return '/^' . $pattern . '$/';
    }

    /**
     * @param string $valueA
     * @param string $valueB
     * @param string $direction
     * @return int
     */
    private function compareStringValues($valueA, $valueB, $direction)
    {
        $result = strnatcasecmp($valueA, $valueB);
        return self::SORT_ORDER_ASC == $direction ?
            $result :
            $result * -1;
    }

    private function compareArrays(array $valueA, array $valueB, $direction)
    {
        $factor = self::SORT_ORDER_ASC == $direction ?
            1 :
            -1;
        if (!$valueA) {
            return self::SORT_RESULT_A_LESS_THEN_B * $factor;
        }
        if (!$valueB) {
            return self::SORT_RESULT_A_MORE_THEN_B * $factor;
        }
        $arrayA = array_values($valueA);
        $arrayB = array_values($valueB);
        foreach ($arrayA as $i => $a) {
            if (!isset($arrayB[$i])) {
                return self::SORT_RESULT_A_MORE_THEN_B * $factor;
            }
            $result = $this->compareStringValues($a, $arrayB[$i], $direction);
            if ($result !== 0) {
                return $result;
            }
        }
        if (count($arrayB) > count($arrayA)) {
            return self::SORT_RESULT_A_LESS_THEN_B * $factor;
        }
        return 0;
    }
}
