<?php


class VinaiKopp_PostCodeFilter_Block_Adminhtml_Postcodefilter_New
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_blockGroup = 'vinaikopp_postcodefilter';
    protected $_controller = 'adminhtml_postcodefilter';
    protected $_mode = 'new';

    public function getHeaderText()
    {
        return $this->__('New Post Code Filter Rule');
    }
}
