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
 * @package     Magestore_Giftvoucher
 * @module     Giftvoucher
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Giftvoucher_Block_Adminhtml_Gifttemplate_Edit
 */
class Magestore_Giftvoucher_Block_Adminhtml_Gifttemplate_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    /**
     * Magestore_Giftvoucher_Block_Adminhtml_Gifttemplate_Edit constructor.
     */
    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'giftvoucher';
        $this->_controller = 'adminhtml_gifttemplate';

        $this->_updateButton('save', 'label', Mage::helper('giftvoucher')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('giftvoucher')->__('Delete'));


		$this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);
        $this->_addButton('preview', array(
            'label' => Mage::helper('adminhtml')->__('Preview'),
            'onclick' => 'previewImage()',
            'class' => 'save',
                ), -100);



        $this->_formScripts[] = "
                function saveAndContinueEdit(){
                editForm.submit('" . $this->getUrl('*/*/save', array(
                    'id' => $this->getRequest()->getParam('id'),
                    'back' => 'edit'
                )) . "');
                    
            }
            
            function removeImage(element){
                
                new Ajax.Request('"
                . $this->getUrl('*/*/removeimage', array('_current' => true))
                . "', {
                            parameters: {
                                         form_key: FORM_KEY,
                                         value: element,
                                         
                                         },
                            evalScripts: true,
                            onSuccess: function(transport) {
                                if(transport.responseText=='success'){
                                 $(element).remove();
                                 if(!$('fileuploaded').down('img')) $('fileuploaded').up('tr').hide();
                                }
                            }
                        });
            }
            function previewImage(element){
                edit_form=$('edit_form').serialize(true);
                form_data=Object.toJSON(edit_form);
                new Ajax.Request('"
                . $this->getUrl('*/*/previewimage', array('_current' => true))
                . "', {
                            method:'post',
                            parameters: {
                                
                                         form_key: FORM_KEY,
                                         value: element,
                                         form_data:form_data  
                                         },
                            evalScripts: true,
                            onSuccess: function(transport) {
                               TINY.box.show();
                                $('tinycontent').update(transport.responseText);
                            }
                        });
            }
            

            Event.observe(window, 'load', function(){changePattern();});
            function changePattern(){
				$('giftcard-notes-center').hide();
				$('giftcard-notes-top').hide();
				$('giftcard-notes-left').hide();
                $('giftcard-notes-simple').hide();
				$('gifttemplate_form').setStyle({'height': 'auto', 'min-height': '600px'});
                $('demo_pattern').setStyle({top: '15px'});
                
                template_id = $('design_pattern').value;
                $('demo_pattern').down('img').src='".Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA). '/giftvoucher/template/pattern/GC_'."'+template_id+'.jpg';                
            }
        ";
    }

    /**
     * @return string
     */
    public function getHeaderText() {
        if (Mage::registry('gifttemplate_data') && Mage::registry('gifttemplate_data')->getId()) {
            return Mage::helper('giftvoucher')->__("Edit Gift Card Template '%s'", $this->htmlEscape(Mage::registry('gifttemplate_data')->getTemplateName()));
        } else {
            return Mage::helper('giftvoucher')->__('New Gift Card Template');
        }
    }

}