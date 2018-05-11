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

class Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Transferreditem_Importtransferreditem
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Abstracttab
{
    /**
     *
     * @var string
     */
    protected $modalId = 'import_transferred_item_modal';

    /**
     * @var Magestore_Purchaseordersuccess_Model_Return
     */
    protected $returnRequest;

    /*
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('purchaseordersuccess/return/edit/tab/transferred_item/import_transfer_item.phtml');
        $this->_prepareNotices();
        $this->returnRequest = Mage::registry('current_return_request');
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!Mage::helper('purchaseordersuccess')->isModuleEnabled('Magestore_Inventorysuccess'))
            return '';
        $status = $this->returnRequest->getStatus();
        if ($status == ReturnStatus::STATUS_PROCESSING || $status == ReturnStatus::STATUS_COMPLETED)
            return parent::_toHtml();
        else
            return '';
    }

    /**
     * prepare notification messages
     *
     */
    protected function _prepareNotices()
    {
        $notices = array(
            'invalid_file_message' => $this->__('Invalid file type!'),
            'choose_file_upload_message' => $this->__('Please choose CSV file to import.'),
            'importing_error_message' => $this->__('There was an error while importing.'),
            'upload_failed_message' => $this->__('There was an error attempting to upload the file.'),
            'upload_canceled_message' => $this->__('The upload has been canceled by the user or the browser dropped the connection.'),
            'upload_select_import_date' =>$this->__('Please select transfer date.'),
            'upload_select_warehouse_to_transfer' =>$this->__('Please select Warehouse to transfer.')
        );
        $this->addData($notices);
    }

    /**
     * Get csv sample dowload link
     *
     * @return string
     */
    public function getCsvSampleLink(){
        return $this->getUrl(
            "*/purchaseordersuccess_return_transferreditem_import/downloadSample",
            array("_current" => true, 'supplier_id' => $this->returnRequest->getSupplierId())
        );
    }

    /**
     * Get import url
     *
     * @return string
     */
    public function getImportLink(){
        return $this->getUrl(
            "*/purchaseordersuccess_return_transferreditem_import/import",
            array("_current" => true, 'supplier_id' => $this->returnRequest->getSupplierId(), 'id' =>$this->getRequest()->getParam('id'))
        );
    }

//    /**
//     * @return string
//     */
//    public function getJsParentObjectName(){
//        return $this->getLayout()
//            ->createBlock('purchaseordersuccess/adminhtml_purchaseorder_edit_tab_transferreditem_grid')
//            ->getJsObjectName();
//    }

    /**
     * Add Returned Time Field
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function addTransferredTimeField()
    {
        $html = $this->addField('import_transferred_at',
            'date',
            array(
                'name' => 'import_transferred_at',
                'time' => false,
                'label' => $this->__('Delivery Date'),
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'required' => true,
                'class' => 'validate-date',
                'min_date' => $this->returnRequest->getReturnedAt(),
                'value' => new Zend_Date($this->returnRequest->getReturnedAt(), Varien_Date::DATE_INTERNAL_FORMAT),
                'readonly' => true
            )
        );
        $html .= $this->addField('is_subtract_import',
            'checkbox',
            array(
                'name' => 'is_subtract_import',
                'label' => $this->__('Subtract stock on warehouse'),
                'checked' => true
            )
        );
        $html .= $this->addField('transferred_warehouse_id',
            'hidden',
            array(
                'name' => 'transferred_warehouse_id',
                'label' => $this->__(''),
                'value' => $this->returnRequest->getWarehouseId()
            )
        );
        return $html;
    }

    public function getForm()
    {
        if (!$this->form)
            $this->form = $this->getLayout()
                ->createBlock('Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Transferreditem_Transferitem_Form');
        return $this->form;
    }
}