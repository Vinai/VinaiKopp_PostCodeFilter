<?php


class VinaiKopp_PostCodeFilter_Block_Adminhtml_Postcodefilter_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_pagerVisibility = false;

    /**
     * @var mixed[]
     */
    private $currentItemParams;

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('vinaikopp_postcodefilter/ruleCollection');
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
            'sortable' => true,
        ]);
        $this->addColumn('customer_groups', [
            'header' => $this->__('Customer Groups'),
            'index' => 'customer_groups',
            'type' => 'options',
            'options' => $this->getCustomerGroupsOptions(),
            'sortable' => true,
        ]);
        $this->addColumn('post_codes', [
            'header' => $this->__('Allowed Post Codes'),
            'index' => 'post_codes',
            'type' => 'options',
            'options' => ['required-dummy-option'],
            'show_missing_option_values' => true,
            'filter' => false,
            'sortable' => true,
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
                        'field' => 'country'
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
}
