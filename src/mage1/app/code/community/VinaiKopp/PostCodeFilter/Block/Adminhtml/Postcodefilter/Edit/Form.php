<?php

class VinaiKopp_PostCodeFilter_Block_Adminhtml_Postcodefilter_Edit_Form
    extends VinaiKopp_PostCodeFilter_Block_Adminhtml_Postcodefilter_New_Form
{
    protected function getFormUrl()
    {
        $rule = $this->getRule();
        $params = [
            'old_country' => $rule->getData('country'),
            'old_customer_group_ids' => implode(',', $rule->getData('customer_groups'))
        ];
        return $this->getUrl('*/*/update', $params);
    }

    public function setForm(Varien_Data_Form $form)
    {
        $values = $this->getFormValues();
        $form->setValues($values);
        return parent::setForm($form);
    }

    private function getFormValues()
    {
        $rule = $this->getRule();
        return [
            'country' => $rule->getData('country'),
            'customer_group_ids' => $rule->getData('customer_groups'),
            'post_codes' => implode(PHP_EOL, $rule->getData('post_codes'))
        ];
    }

    /**
     * @return Varien_Object
     */
    private function getRule()
    {
        return Mage::registry('current_rule');
    }


}
