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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Adjuststock Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Adjuststock_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Adjuststock_Edit constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'inventorysuccess';
        $this->_controller = 'adminhtml_adjuststock';
        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $adjustStockId = $this->getRequest()->getParam('id');
        if($adjustStockId) {
            $adjustStock = Mage::getModel('inventorysuccess/adjuststock')->load($this->getRequest()->getParam('id'));
            if($adjustStock->getStatus() == Magestore_Inventorysuccess_Model_Adjuststock::STATUS_PENDING){
                $this->_addButton('saveandcontinue', array(
                    'label'        => Mage::helper('adminhtml')->__('Save And Continue Edit'),
                    'onclick'    => 'saveAndContinueEdit()',
                    'class'        => 'save',
                ), -100);

                $permission = Magestore_Coresuccess_Model_Service::permissionService();
                if ($permission->checkPermission(
                    'inventorysuccess/stockcontrol/confirm_adjuststock'
                )) {
                    $this->_addButton('confirm', array(
                        'label' => Mage::helper('adminhtml')->__('Adjust'),
                        'onclick' => 'adjust()',
                        'class' => 'btn-adjust',
                    ), -100);
                }
            }
            if($adjustStock->getStatus() == Magestore_Inventorysuccess_Model_Adjuststock::STATUS_COMPLETED){
                $this->_removeButton('save');
            }
        }
        if(!$adjustStockId){
            $this->_removeButton('save');
            $this->_addButton('saveandcontinue', array(
                'label'        => Mage::helper('adminhtml')->__('Start to Adjust'),
                'onclick'    => 'saveAndContinueEdit()',
                'class'        => 'btn-next-step',
            ), -100);
        }

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('adjuststock_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'adjuststock_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'adjuststock_content');
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
            
            function adjust(){
                var r = confirm('".Mage::helper('inventorysuccess')->__('Are you sure you want to adjust stock?')."');
                if (r == true) {
                    editForm.submit($('edit_form').action+'back/confirm/');
                } 
                return false;
            }
            
            function exportProduct(){
                editForm.submit($('edit_form').action+'back/export/');
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
        if (Mage::registry('adjuststock_data')
            && Mage::registry('adjuststock_data')->getId()
        ) {
            return Mage::helper('inventorysuccess')->__("Edit Stock Adjustment '%s'",
                                                $this->escapeHtml(Mage::registry('adjuststock_data')->getAdjuststockCode())
            );
        }
        return Mage::helper('inventorysuccess')->__('New Stock Adjustment');
    }
}