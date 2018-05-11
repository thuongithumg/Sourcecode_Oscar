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
 * @package     Magestore_RewardPointsLoyaltyLevel
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Rewardpointsloyaltylevel Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_RewardPointsLoyaltyLevel
 * @author      Magestore Developer
 */
class Magestore_RewardPointsLoyaltyLevel_Block_Adminhtml_Rewardpointsloyaltylevel_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'rewardpointsloyaltylevel';
        $this->_controller = 'adminhtml_rewardpointsloyaltylevel';

        $this->_updateButton('save', 'label', Mage::helper('rewardpointsloyaltylevel')->__('Save'));
//        $this->_updateButton('delete', 'label', Mage::helper('rewardpointsloyaltylevel')->__('Delete Item'));
        $this->_removeButton('delete');
        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
                ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('rewardpointsloyaltylevel_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'rewardpointsloyaltylevel_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'rewardpointsloyaltylevel_content');
            }

            function saveAndContinueEdit(){
                var current_tab=getTabCurrentId();                
                editForm.submit($('edit_form').action+'back/'+current_tab+'/');
            }
            function getTabCurrentId(){
                var form_section=$('rewardpointsloyaltylevel_tabs_form_section').className;
                if(form_section.search('active')!=-1)
                    return 'form';
                else return 'benefit';
            }
        ";
        $this->_formScripts[] = "
            if($('level_create_new')) $('level_from').up('tr').hide();
            if($('level_new')) $('level_new').value = '1';
                ";
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText() {
        if (Mage::registry('rewardpointsloyaltylevel_data') && Mage::registry('rewardpointsloyaltylevel_data')->getId()
        ) {
            return Mage::helper('rewardpointsloyaltylevel')->__("Edit Level '%s'", $this->htmlEscape(Mage::registry('rewardpointsloyaltylevel_data')->getLevelName())
            );
        }
        return Mage::helper('rewardpointsloyaltylevel')->__('Add Level');
    }

}
