<?php


namespace VinaiKopp\PostCodeFilter;

use VinaiKopp_PostCodeFilter_Model_RuleCollection;

class RuleCollectionSpy extends \VinaiKopp_PostCodeFilter_Model_RuleCollection
{
    public $setOrderWasCalled = false;

    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->setOrderWasCalled = true;
        return parent::setOrder($field, $direction);
    }
}
