<?php


class VinaiKopp_PostCodeFilter_Block_Adminhtml_Postcodefilter
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected $_blockGroup = 'vinaikopp_postcodefilter';
    protected $_controller = 'adminhtml_postcodefilter';

    public function getHeaderText()
    {
        return $this->__('Post Code Filter');
    }
}
