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
 * Stocktaking Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Edit constructor.
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'inventorysuccess';
        $this->_controller = 'adminhtml_stocktaking';
        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('save');
        $this->_removeButton('back');
        $stocktakingId = $this->getRequest()->getParam('id');
        if($stocktakingId) {
            $stocktaking = $this->getStocktaking();
            if($stocktaking->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PENDING){
                $this->addPendingButtons();
            }
            if($stocktaking->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PROCESSING){
                $this->addProcessingButtons();
            }
            if($stocktaking->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_VERIFIED){
                $this->addVerifyButtons();
            }

            if($stocktaking->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_COMPLETED){
                $this->addCompleteButtons();
            }
            if($stocktaking->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_CANCELED){
                $this->addCancelButtons();
            }
        }
        if(!$stocktakingId){
            $this->addNewButtons();
        }
        $this->_formScripts[] = $this->addJsFunctions();
    }

    /**
     * add buttons when stocktaking status is pending
     */
    public function addPendingButtons(){
        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('adminhtml')->__('Save'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -99);

        $this->_addButton('start', array(
            'label'        => Mage::helper('adminhtml')->__('Start Stocktaking'),
            'onclick'    => 'startStocktake()',
            'class'        => 'btn-next-step',
        ), -100);

        $this->_addButton('cancel', array(
            'label'        => Mage::helper('adminhtml')->__('Cancel'),
            'onclick'    => 'cancel()',
            'class'        => 'btn-next-step',
        ), -100);

    }

    /**
     * add buttons when stocktaking status is processing
     */
    public function addCancelButtons(){
        $this->_addButton('deletestock', array(
            'label'        => Mage::helper('adminhtml')->__('Delete'),
            'onclick'    => 'deletestock()',
            'class'        => 'btn-next-step',
        ), -100);

        $this->_addButton('reopen', array(
            'label'        => Mage::helper('adminhtml')->__('Re-Open'),
            'onclick'    => 'reopen()',
            'class'        => 'btn-next-step',
        ), -100);
    }

    /**
     * add buttons when stocktaking status is processing
     */
    public function addProcessingButtons(){
        $permission = Magestore_Coresuccess_Model_Service::permissionService();
        if ($permission->checkPermission(
            'inventorysuccess/stockcontrol/verify_stocktaking'
        )) {
            $this->_addButton('verify', array(
                'label' => Mage::helper('adminhtml')->__('Complete Data Entry'),
                'onclick' => 'completeDataEntry()',
                'class' => 'btn-next-step',
            ), -98);
        }

        if ($permission->checkPermission(
            'inventorysuccess/stockcontrol/confirm_stocktaking'
        )) {
            $this->_addButton('confirm', array(
                'label' => Mage::helper('adminhtml')->__('Complete Stocktaking'),
                'onclick' => 'completeStocktake()',
                'class' => 'btn-next-step',
            ), -99);
        }

        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('adminhtml')->__('Save'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);
    }

    /**
     * add buttons when create a stocking
     */
    public function addNewButtons(){
        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('adminhtml')->__('Prepare Product List'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'btn-next-step',
        ), -99);

        $this->_addButton('start', array(
            'label'        => Mage::helper('adminhtml')->__('Start Stocktaking'),
            'onclick'    => 'startStocktake()',
            'class'        => 'btn-next-step',
        ), -100);
    }

    /**
     * add buttons when stocktaking status is verify
     */
    public function addVerifyButtons(){
        $this->_addButton('redata', array(
            'label'        => Mage::helper('adminhtml')->__('Re-entry Data'),
            'onclick'    => 'reDataEntry()',
            'class'        => 'btn back',
        ), -98);

        $permission = Magestore_Coresuccess_Model_Service::permissionService();
        if ($permission->checkPermission(
            'inventorysuccess/stockcontrol/confirm_stocktaking'
        )) {
            $this->_addButton('confirm', array(
                'label' => Mage::helper('adminhtml')->__('Complete Stocktaking'),
                'onclick' => 'completeStocktake()',
                'class' => 'save',
            ), -100);
        }
    }

    /**
     * add buttons when stocktaking status is complete
     */
    public function addCompleteButtons(){
        $this->_addButton('redata', array(
            'label'        => Mage::helper('adminhtml')->__('Adjust Stock'),
            'onclick'    => 'adjustStock()',
            'class'        => 'btn-adjust',
        ), -98);

        $this->_addButton('confirm', array(
            'label'        => Mage::helper('adminhtml')->__('Download Difference List'),
            'onclick'    => 'downloadDifferentList()',
            'class'        => 'btn-print',
        ), -100);
    }

    /**
     * add button js functions
     *
     * @return string
     */
    public function addJsFunctions(){
        $adjustUrl = $this->getAdjustUrl();
        $exportUrl = $this->getExportUrl();
        $functions = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('stocktaking_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'stocktaking_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'stocktaking_content');
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
            
            function startStocktake(){
                editForm.submit($('edit_form').action+'back/start/');
            }
            function cancel(){
                editForm.submit($('edit_form').action+'back/cancel/');
            }
            function reopen(){
                editForm.submit($('edit_form').action+'back/reopen/');
            }
            function deletestock(){
                editForm.submit($('edit_form').action+'back/delete/');
            }
            function completeDataEntry(){
                editForm.submit($('edit_form').action+'back/verify/');
            }
            
            function reDataEntry(){
                editForm.submit($('edit_form').action+'back/redata/');
            }
            
            function completeStocktake(){
                var r = confirm('".Mage::helper('inventorysuccess')->__('Are you sure you want to complete stocktaking?')."');
                if (r == true) {
                    editForm.submit($('edit_form').action+'back/confirm/');
                } 
                return false;
            }
            
            function adjustStock(){
                var r = confirm('".Mage::helper('inventorysuccess')->__('Are you sure you want to adjust stock?')."');
                if (r == true) {
                    window.location = '".$adjustUrl."'
                } 
                return false;
            }
            
            function exportProduct(){
                editForm.submit($('edit_form').action+'back/export/');
            }
            
            function downloadDifferentList(){
                window.location = '".$exportUrl."'
            }
        ";
        return $functions;
    }

    /**
     * get adjust url
     *
     * @return Magestore_Inventory_Model_Stocktaking
     */
    public function getAdjustUrl()
    {
        $id = $this->getRequest()->getParam('id');
        $warehouseId = $this->getStocktaking()->getData('warehouse_id');
        $stocktakingCode = $this->getStocktaking()->getData('stocktaking_code');
        $adjustUrl = $this->getUrl('*/inventorysuccess_stocktaking_adjust/request',
            array(
                '_secure' => true,
                'id' => $id,
                'warehouse_id' => $warehouseId,
                'stocktaking_code' => $stocktakingCode
            ));

        return $adjustUrl;
    }

    /**
     * get export url
     *
     * @return Magestore_Inventory_Model_Stocktaking
     */
    public function getExportUrl()
    {
        $id = $this->getRequest()->getParam('id');
        $exportUrl = $this->getUrl('*/inventorysuccess_stocktaking_product/exportDifferent',
            array(
                '_secure' => true,
                'id' => $id,
            ));

        return $exportUrl;
    }

    /**
     * get current stocktaking
     *
     * @return Magestore_Inventory_Model_Stocktaking
     */
    public function getStocktaking()
    {
        if (Mage::registry('stocktaking_data')
            && Mage::registry('stocktaking_data')->getId()
        ) {
            return Mage::registry('stocktaking_data');
        }
        return Mage::getModel('inventorysuccess/stocktaking')->load($this->getRequest()->getParam('id'));
    }
    
    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('stocktaking_data')
            && Mage::registry('stocktaking_data')->getId()
        ) {
            return Mage::helper('inventorysuccess')->__("Edit Stocktaking '%s'",
                                                $this->htmlEscape(Mage::registry('stocktaking_data')->getStocktakingCode())
            );
        }
        return Mage::helper('inventorysuccess')->__('New Stocktaking');
    }

    /**
     * get html to show on header
     *
     * @return string
     */
    public function getHeaderHtml()
    {
        return $this->getLayout()->createBlock('inventorysuccess/adminhtml_stocktaking_timeline')
                  ->setTemplate('inventorysuccess/timeline/step.phtml')->toHtml();
    }
}