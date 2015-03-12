<?php


class VinaiKopp_PostCodeFilter_Block_Adminhtml_Postcodefilter_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_blockGroup = 'vinaikopp_postcodefilter';
    protected $_controller = 'adminhtml_postcodefilter';
    protected $_mode = 'edit';

    public function getHeaderText()
    {
        return $this->__('Edit Post Code Filter Rule');
    }
}
