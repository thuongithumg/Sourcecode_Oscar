<?php
class Magestore_Debugsuccess_Block_Adminhtml_Debug_Movement extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_debug_movement';
        $this->_blockGroup = 'debugsuccess';
        $this->_headerText = Mage::helper('debugsuccess')->__('Collect historics');
        parent::__construct();
        $this->_removeButton('add');
    }
    public function getHeaderText() {
        return Mage::helper('debugsuccess')->__('Collect historics');
    }
}