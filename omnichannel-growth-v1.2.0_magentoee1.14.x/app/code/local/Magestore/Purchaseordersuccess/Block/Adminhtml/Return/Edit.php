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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseordersuccess Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
use Magestore_Purchaseordersuccess_Model_Return_Options_Status as ReturnStatus;

class Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Return
     */
    protected $returnRequest;

    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'return_id';
        $this->_controller = 'adminhtml_return';
        $this->_blockGroup = 'purchaseordersuccess';

//        $this->_removeButton('back');
        $this->_removeButton('delete');
        $this->_removeButton('reset');

        $this->returnRequest = Mage::registry('current_return_request');
        $status = $this->returnRequest->getStatus();
        $purchaseId = $this->returnRequest->getReturnOrderId();

        if ($this->returnRequest->getReturnOrderId()) {

            $printUrl = $this->getUrl('*/*/print', array('id' => $purchaseId));
            $this->_addButton('print', array(
                'label' => $this->__('Print'),
                'onclick' => sprintf(
                    "window.open('%s', 'PrintWindow', 'width=500,height=500,top=200,left=200').print()", $printUrl
                ),
                'class' => 'print',
            ), -1);

            if ($status != ReturnStatus::STATUS_CANCELED) {
                $sendEmailLabel = 'Send Email';
                if ($status == ReturnStatus::STATUS_PENDING)
                    $sendEmailLabel = 'Send Request';
                $sendEmailUrl = $this->getUrl('*/*/sendrequest', array('id' => $purchaseId));
                $this->_addButton('send_email', array(
                    'label' => $this->__($sendEmailLabel),
                    'onclick' => sprintf(
                        "confirmSetLocation('%s', '%s')", $this->__('Are you sure you want to do it?'), $sendEmailUrl
                    ),
                    'class' => 'send',
                ), -1);

                if ($status != ReturnStatus::STATUS_COMPLETED) {
                    $this->_addButton('cancel', array(
                        'label' => $this->__('Cancel'),
                        'onclick' => "if(confirm('" . $this->__('Are you sure you want to do this?') . "')){
                            editForm.submit($('edit_form').action+'action_type/cancel/')
                        }",
                        'class' => 'delete',
                    ), -1);
                }

                if ($status == ReturnStatus::STATUS_PENDING) {
                    $this->_addButton('confirm', array(
                        'label' => $this->__('Confirm Return Request'),
                        'onclick' => "if(confirm('" . $this->__('Are you sure you want to make this return request?') . "')){
                            editForm.submit($('edit_form').action+'action_type/confirm/')
                        }",
                        'class' => 'save',
                    ), -1);
                }

                if(Mage::helper('purchaseordersuccess')->isModuleEnabled('Magestore_Inventorysuccess')) {
                    if ($status == ReturnStatus::STATUS_PROCESSING || $status == ReturnStatus::STATUS_COMPLETED) {
                        $this->_addButton('transfer_item', array(
                            'label' => $this->__('Delivery Items'),
                            'onclick' => "transferItem.gridJsObject.reload()",
                            'class' => 'save',
                            'id' => 'transfer_item_button_top',
                        ), 1);
                    }
                }

                if ($status == ReturnStatus::STATUS_PROCESSING) {
                    $this->_addButton('complete', array(
                        'label' => $this->__('Complete PO'),
                        'onclick' => "if(confirm('" . $this->__('Are you sure you want to complete this return request?') . "')){
                        editForm.submit($('edit_form').action+'action_type/complete/')
                    }",
                        'class' => 'save',
                    ), 1);
                }

            } else {
                $this->_removeButton('save');

                $this->_addButton('delete', array(
                    'label' => $this->__('Delete'),
                    'onclick' => "if(confirm('" . $this->__('Are you sure you want to do this?') . "')){
                            editForm.submit($('edit_form').action+'action_type/delete/')
                        }",
                    'class' => 'delete',
                ), -1);
            }
        } else {
            $this->_updateButton('save', 'label', $this->__('Prepare Product List'));
        }

        $this->checkButtonPermission();
    }

    protected function checkButtonPermission() {
        $control = 'return';
        if(!Mage::getSingleton('admin/session')->isAllowed('purchaseordersuccess/'.$control.'/save')) {
            $this->_removeButton('save');
        }
        if(!Mage::getSingleton('admin/session')->isAllowed('purchaseordersuccess/'.$control.'/delete')) {
            $this->_removeButton('delete');
        }
        if(!Mage::getSingleton('admin/session')->isAllowed('purchaseordersuccess/'.$control.'/cancel')) {
            $this->_removeButton('cancel');
        }
        if(!Mage::getSingleton('admin/session')->isAllowed('purchaseordersuccess/'.$control.'/convert')) {
            $this->_removeButton('convert');
        }
        if(!Mage::getSingleton('admin/session')->isAllowed('purchaseordersuccess/'.$control.'/complete')) {
            $this->_removeButton('complete');
        }
        if(!Mage::getSingleton('admin/session')->isAllowed('purchaseordersuccess/'.$control.'/transfer')) {
            $this->_removeButton('transfer_item');
        }
        if(!Mage::getSingleton('admin/session')->isAllowed('purchaseordersuccess/'.$control.'/send_request')) {
            $this->_removeButton('send_email');
        }
        if(!Mage::getSingleton('admin/session')->isAllowed('purchaseordersuccess/'.$control.'/confirm')) {
            $this->_removeButton('confirm');
        }
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->returnRequest->getReturnOrderId()) {
            $code = $this->returnRequest->getReturnCode();
            $code = $code ? $code : $this->returnRequest->getId();
            return $this->__('View Return Request #%s', $code);
        } else {
            return $this->__('New Return Request');
        }
    }


    /**
     * get html to show on header
     *
     * @return string
     */
    public function getHeaderHtml()
    {
        return $this->getLayout()->createBlock('purchaseordersuccess/adminhtml_return_timeline')
            ->setTemplate('purchaseordersuccess/timeline/step.phtml')->toHtml();
    }
}