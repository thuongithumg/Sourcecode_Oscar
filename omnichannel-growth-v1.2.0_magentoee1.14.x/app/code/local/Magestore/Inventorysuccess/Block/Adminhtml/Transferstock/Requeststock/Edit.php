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
 * Inventorysuccess Edit Block
 *
 * @category     Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Requeststock_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Requeststock_Edit constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->_objectId   = 'id';
        $this->_blockGroup = 'inventorysuccess';
        $this->_controller = 'adminhtml_transferstock_requeststock';
        $this->_removeButton('reset');
        $this->_removeButton('delete');
        $this->_removeButton('back');
        /** @var Magestore_Inventorysuccess_Model_Transferstock $model */
        $model = Mage::registry('requeststock_data');
        if (!$model->getId()) {
            $this->_removeButton('save');
            $this->_addButton('prepare_product_list', array(
                'label' => Mage::helper('adminhtml')->__('Prepare Product List'),
                'onclick' => 'saveGeneral()',
                'class' => 'btn-next-step',
            ), -100);
        } elseif ($model->getStatus() == Magestore_Inventorysuccess_Model_Transferstock::STATUS_PENDING) {

            $this->_addButton('send_mail', array(
                'label' => Mage::helper('adminhtml')->__('Send email'),
                'onclick' => 'sendMail()',
                'class' => 'btn-next-step',
            ), -100);

            $this->_addButton('cancel_transfer', array(
                'label' => Mage::helper('adminhtml')->__('Cancel'),
                'onclick' => 'cancelTransfer()',
                'class' => 'delete',
            ), -200);

            $this->_addButton('start_request_stock', array(
                'label' => Mage::helper('adminhtml')->__('Start to request'),
                'onclick' => 'startRequestStock()',
                'class' => 'btn-next-step',
            ), -100);
        } elseif ($model->getStatus() == Magestore_Inventorysuccess_Model_Transferstock::STATUS_PROCESSING) {
            $this->_removeButton('save');
            $this->_addButton('mark_as_completed', array(
                'label' => Mage::helper('adminhtml')->__('Mark as Completed'),
                'onclick' => 'markAsCompleted()',
                'class' => 'save',
            ), -100);
        }
        elseif ($model->getStatus() == Magestore_Inventorysuccess_Model_Transferstock::STATUS_CANCELED) {
            $this->_removeButton('save');
            $this->_addButton('re_open', array(
                'label' => Mage::helper('adminhtml')->__('Re-Open'),
                'onclick' => 'reOpen()',
                'class' => 'btn-next-step',
            ), -100);
            $this->_addButton('delete_transfer', array(
                'label' => Mage::helper('adminhtml')->__('Delete'),
                'onclick' => 'deleteTransfer()',
                'class' => 'delete',
            ), -100);
        }
        else {
            $this->_removeButton('save');
        }
        $this->_formScripts[]
            = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('inventorysuccess_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'inventorysuccess_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'inventorysuccess_content');
            }
            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
            function saveGeneral(){
                editForm.submit($('edit_form').action+'back/edit/step/save_general');
            }
            function startRequestStock(){
                editForm.submit($('edit_form').action+'back/edit/step/start_request');
            }
            function markAsCompleted(){
                editForm.submit($('edit_form').action+'back/edit/step/complete');
            }
            function sendMail(){
                editForm.submit($('edit_form').action+'back/edit/step/send_mail');
            }
            function cancelTransfer(){
                editForm.submit($('edit_form').action+'back/edit/step/cancel_transfer');
            }
            function reOpen(){
                editForm.submit($('edit_form').action+'back/edit/step/re_open');
            }
            function deleteTransfer(){
                editForm.submit($('edit_form').action+'back/edit/step/delete_transfer');
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
        $model = Mage::registry('requeststock_data');
        if ($model && $model->getId()) {
            return Mage::helper('inventorysuccess')->__("Stock Request #%s (%s)",
                $this->escapeHtml($model->getTransferstockCode()),$model->getData('status')
            );
        }
        return Mage::helper('inventorysuccess')->__('New Stock Request');
    }
}