<?php

class VinaiKopp_PostCodeFilter_Block_Adminhtml_Postcodefilter_New_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = $this->createForm();
        $this->addFields($form);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @return Varien_Data_Form
     */
    private function createForm()
    {
        return new Varien_Data_Form([
            'id' => 'edit_form',
            'action' => $this->getFormUrl(),
            'method' => 'post',
            'use_container' => true
        ]);
    }
    
    protected function getFormUrl()
    {
        return $this->getUrl('*/*/create');
    }

    protected function addFields(Varien_Data_Form $form)
    {
        $fieldset = $form->addFieldset('main', [
            'legend' => $this->__('Post Code Rule')
        ]);
        $fieldset->addField('country', 'select', [
            'label' => $this->__('Country'),
            'name' => 'country',
            'values' => $this->getAllowedCountryOptions(),
            'required' => true
        ]);
        $fieldset->addField('customer_group_ids', 'multiselect', [
            'label' => $this->__('Customer Groups'),
            'name' => 'customer_group_ids',
            'note' => $this->__('Alt-Click (Win) or Cmd-Click (OS X) to select multiple'),
            'values' => $this->getCustomerGroupOptions(),
            'required' => true
        ]);
        $fieldset->addField('post_codes', 'textarea', [
            'label' => $this->__('Allowed Post Codes'),
            'name' => 'post_codes',
            'note' => $this->__('Newline or comma separated list of allowed post codes for the selected country')
        ]);
    }

    /**
     * @return array[]
     */
    private function getAllowedCountryOptions()
    {
        return Mage::getResourceModel('directory/country_collection')->loadByStore()->toOptionArray();
    }

    /**
     * @return array[]
     */
    private function getCustomerGroupOptions()
    {
        return Mage::getResourceModel('customer/group_collection')->toOptionArray();
    }

}
