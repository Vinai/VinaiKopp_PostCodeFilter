<?php


class VinaiKopp_PostCodeFilter_Block_Adminhtml_Postcodefilter_Edit_Form
    extends VinaiKopp_PostCodeFilter_Block_Adminhtml_Postcodefilter_New_Form
{
    protected function getFormUrl()
    {
        return $this->getUrl('*/*/update');
    }
}
