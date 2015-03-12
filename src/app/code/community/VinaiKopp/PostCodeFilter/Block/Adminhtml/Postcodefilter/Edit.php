<?php


class VinaiKopp_PostCodeFilter_Block_Adminhtml_Postcodefilter_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_blockGroup = 'vinaikopp_postcodefilter';
    protected $_controller = 'adminhtml_postcodefilter';
    protected $_mode = 'edit';
    protected $_objectId = 'country';

    public function getHeaderText()
    {
        return $this->__('Edit Post Code Filter Rule');
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', [
            $this->_objectId => $this->getRequest()->getParam($this->_objectId),
            'customer_group_ids' => $this->getRequest()->getParam('customer_group_ids')
        ]);
    }
}
