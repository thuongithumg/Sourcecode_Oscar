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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Created by PhpStorm.
 * User: Quoc Viet
 * Date: 07/07/2015
 * Time: 9:58 SA
 */

class Magestore_Webpos_Block_Adminhtml_Role_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'webpos';
        $this->_controller = 'adminhtml_role';

        $this->_updateButton('save', 'label', Mage::helper('webpos')->__('Save Role'));
        $this->_updateButton('delete', 'label', Mage::helper('webpos')->__('Delete Role'));

        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('role_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'role_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'role_content');
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('role_data')
            && Mage::registry('role_data')->getId()
        ) {
            return Mage::helper('webpos')->__("Edit Role '%s'",
                $this->htmlEscape(Mage::registry('role_data')->getDisplayName())
            );
        }
        return Mage::helper('webpos')->__('Add Role');
    }
}