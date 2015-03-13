<?php


use VinaiKopp\PostCodeFilter\Query\RuleResult;
use VinaiKopp\PostCodeFilter\UseCases\AdminViewsRuleList;

class VinaiKopp_PostCodeFilter_Block_Adminhtml_Postcodefilter_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_pagerVisibility = false;
    protected $_filterVisibility = false;

    /**
     * @var mixed[]
     */
    private $currentItemParams;

    protected function _prepareCollection()
    {
        $collection = new Varien_Data_Collection();
        $this->addRuleItemsToCollection($collection);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    public function getRowUrl($item)
    {
        /** @var Varien_Object $item */
        $this->currentItemParams = [
            'country' => $item->getData('country'),
            'customer_group_ids' => implode(',', $item->getData('customer_groups')),
        ];
        return $this->getUrl('*/*/edit', $this->currentItemParams);
    }

    protected function _prepareColumns()
    {
        $this->addColumn('country', [
            'header' => $this->__('Country'),
            'width' => '80px',
            'index' => 'country',
            'filter' => false,
            'sortable' => false,
        ]);
        $this->addColumn('customer_groups', [
            'header' => $this->__('Customer Groups'),
            'index' => 'customer_groups',
            'type' => 'options',
            'options' => $this->getCustomerGroupsOptions(),
            'filter' => false,
            'sortable' => false,
        ]);
        $this->addColumn('post_codes', [
            'header' => $this->__('Allowed Post Codes'),
            'index' => 'post_codes',
            'type' => 'options',
            'options' => ['required-dummy'],
            'show_missing_option_values' => true,
            'filter' => false,
            'sortable' => false,
        ]);
        $this->addColumn('action',
            array(
                'header' => $this->__('Action'),
                'width' => '50px',
                'type' => 'action',
                'getter' => 'getCountry',
                'actions' => array(
                    array(
                        'caption' => $this->__('Edit'),
                        'url' => array(
                            'base' => '*/*/edit',
                            'params' => $this->currentItemParams
                        ),
                        //'field' => 'country'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
            ));
        return parent::_prepareColumns();
    }

    /**
     * @return string[]
     */
    private function getCustomerGroupsOptions()
    {
        return Mage::getResourceModel('customer/group_collection')->toOptionHash();
    }

    /**
     * @param Varien_Data_Collection $collection
     */
    private function addRuleItemsToCollection(Varien_Data_Collection $collection)
    {
        $rules = $this->getPostCodeFilterRules();
        foreach ($rules as $rule) {
            $collection->addItem($this->convertRuleToVarienObject($rule));
        }
    }

    /**
     * @param RuleResult $rule
     * @return Varien_Object
     */
    private function convertRuleToVarienObject(RuleResult $rule)
    {
        return new Varien_Object([
            'country' => $rule->getCountryValue(),
            'customer_groups' => $rule->getCustomerGroupIdValues(),
            'post_codes' => $rule->getPostCodeValues()
        ]);
    }

    /**
     * @return RuleResult[]
     */
    private function getPostCodeFilterRules()
    {
        $useCase = $this->getAdminListsRulesUseCase();
        return $useCase->fetchAllRules();
    }

    /**
     * @return AdminViewsRuleList
     */
    private function getAdminListsRulesUseCase()
    {
        /** @var VinaiKopp_PostCodeFilter_Helper_Data $helper */
        $helper = Mage::helper('vinaikopp_postcodefilter');
        return new AdminViewsRuleList($helper->getRuleReader());
    }
}
