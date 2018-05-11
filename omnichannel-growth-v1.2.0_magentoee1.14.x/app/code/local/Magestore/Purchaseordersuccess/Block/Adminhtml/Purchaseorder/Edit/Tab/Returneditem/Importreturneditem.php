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
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Status as PurchaseorderStatus;

class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Returneditem_Importreturneditem
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Abstracttab
{
    /**
     *
     * @var string
     */
    protected $modalId = 'import_returned_item_modal';

    /**
     * @var array
     */
    protected $reloadTabs = array('purchase_order_tabs_returned_item', 'purchase_order_tabs_transferred_item');

    /**
     * @var Magestore_Purchaseordersuccess_Model_Purchaseorder
     */
    protected $purchaseOrder;

    /*
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('purchaseordersuccess/purchaseorder/edit/tab/returned_item/import_returned_item.phtml');
        $this->_prepareNotices();
        $this->purchaseOrder = Mage::registry('current_purchase_order');
    }

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $status = $this->purchaseOrder->getStatus();
        if ($status == PurchaseorderStatus::STATUS_PROCESSING || $status == PurchaseorderStatus::STATUS_COMPLETED)
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
            'upload_select_import_date' => $this->__('Please select return date.')
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
            "*/purchaseordersuccess_purchaseorder_returneditem_import/downloadSample",
            array("_current" => true, 'supplier_id' => $this->purchaseOrder->getSupplierId())
        );
    }

    /**
     * Get import url
     *
     * @return string
     */
    public function getImportLink(){
        return $this->getUrl(
            "*/purchaseordersuccess_purchaseorder_returneditem_import/import",
            array("_current" => true, 'supplier_id' => $this->purchaseOrder->getSupplierId(), 'id' =>$this->getRequest()->getParam('id'))
        );
    }

//    /**
//     * @return string
//     */
//    public function getJsParentObjectName(){
//        return $this->getLayout()
//            ->createBlock('purchaseordersuccess/adminhtml_purchaseorder_edit_tab_purchasesummary_grid')
//            ->getJsObjectName();
//    }

    /**
     * Add Returned Time Field
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function addReturnedTimeField(){

        $html = $this->addField('import_returned_at',
            'date',
            array(
                'name'      => 'import_returned_at',
                'time'      => false,
                'label'     => $this->__('Return Date'),
                'image'     => $this->getSkinUrl('images/grid-cal.gif'),
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format'       => Varien_Date::DATE_INTERNAL_FORMAT,
                'required' => true,
                'class'     => 'validate-date',
                'min_date' => $this->purchaseOrder->getPurchasedAt(),
                'value'     => new Zend_Date($this->purchaseOrder->getPurchasedAt(), Varien_Date::DATE_INTERNAL_FORMAT),
                'readonly'  => true
            )
        );
        return $html;
    }

    public function getForm()
    {
        if (!$this->form)
            $this->form = $this->getLayout()
                ->createBlock('Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Returneditem_Returnitem_Form');
        return $this->form;
    }
}