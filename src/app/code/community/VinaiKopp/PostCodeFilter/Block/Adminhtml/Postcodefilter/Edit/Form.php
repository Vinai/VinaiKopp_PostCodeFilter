<?php

use VinaiKopp\PostCodeFilter\Query\RuleResult;

class VinaiKopp_PostCodeFilter_Block_Adminhtml_Postcodefilter_Edit_Form
    extends VinaiKopp_PostCodeFilter_Block_Adminhtml_Postcodefilter_New_Form
{
    protected function getFormUrl()
    {
        $rule = $this->getRule();
        $params = [
            'old_country' => $rule->getCountryValue(),
            'old_customer_group_ids' => implode(',', $rule->getCustomerGroupIdValues())
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
            'country' => $rule->getCountryValue(),
            'customer_group_ids' => $rule->getCustomerGroupIdValues(),
            'post_codes' => implode(PHP_EOL, $rule->getPostCodeValues())
        ];
    }

    /**
     * @return RuleResult
     */
    private function getRule()
    {
        return Mage::registry('current_rule');
    }


}
