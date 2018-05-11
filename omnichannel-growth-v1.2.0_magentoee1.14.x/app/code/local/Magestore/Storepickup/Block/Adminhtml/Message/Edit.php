<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Storepickup_Block_Adminhtml_Message_Edit
 */
class Magestore_Storepickup_Block_Adminhtml_Message_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Magestore_Storepickup_Block_Adminhtml_Message_Edit constructor.
     */
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'storepickup';        
        $this->_controller = 'adminhtml_message';        
       // $this->_updateButton('save', 'label', Mage::helper('storepickup')->__('Save Item'));
       // $this->_updateButton('delete', 'label', Mage::helper('storepickup')->__('Delete Message'));	
        $this->removeButton('save');
        $this->removeButton('delete');
        $this->removeButton('reset'); 
        $id = Mage::registry('store_data')->getId(); 
        $model = Mage::getModel('storepickup/message');
        $idd = $model->load($id)->getData('store_id');       
        $url = "'";
        $url .=  $this->getUrl('*/storepickup_store/edit', array('id' => $idd));
        $url .= "'";
        $this->_updateButton('back', 'onclick' ,'setLocation('.$url.')');
    }
    public function getUrlViewStore(){
        
    }

    /**
     * @return mixed
     */
    public function getHeaderText()
    {
        if( Mage::registry('store_data') && Mage::registry('store_data')->getId() ) {
            return Mage::helper('storepickup')->__("Edit Message from %s",$this->htmlEscape(Mage::registry('store_data')->getData('name')));
        } else {
            return Mage::helper('storepickup')->__('Add Store');
        }
    }

    /**
     * @param $button_name
     */
    public function removeButton($button_name)
	{
		$this->_removeButton($button_name);
	}
	
}