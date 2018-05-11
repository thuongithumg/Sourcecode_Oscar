<?php

/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess3
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 *
 */
use Magestore_Purchaseordersuccess_Model_Return_Options_Status as ReturnStatus;

class Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Transferreditem_Scan
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Barcode_ReturnScan
{
    protected $_template = 'purchaseordersuccess/return/edit/tab/transferred_item/scan.phtml';

    /**
     * @var array
     */
    protected $reloadTabs = array(
        'return_request_tabs_transferred_item',
        'return_request_tabs_returned_item'
    );

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $status = $this->returnRequest->getStatus();
        if ($status == ReturnStatus::STATUS_PROCESSING || $status == ReturnStatus::STATUS_COMPLETED)
            return parent::_toHtml();
        else
            return '';
    }

    /**
     * Add Received Time Field
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function addScanBarcodeField()
    {
        $html = $this->addField('scan_barcode_transferred_at',
            'date',
            array(
                'name' => 'transferred_at',
                'time' => false,
                'label' => $this->__('Return Date'),
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'required' => true,
                'class'     => 'validate-date',
                'min_date' => $this->returnRequest->getReturnedAt(),
                'value'     => new Zend_Date($this->returnRequest->getReturnedAt(), Varien_Date::DATE_INTERNAL_FORMAT),
                'readonly'  => true
            )
        );
        $html .= $this->addField('scan_barcode_warehouse_id',
            'select',
            array(
                'name'      => 'warehouse_id',
                'time'      => false,
                'label'     => $this->__('Warehouse'),
                'options'   => Mage::getModel('purchaseordersuccess/purchaseorder_options_warehouse')->getOptionHash(),
                'required'  => true
            )
        );
        $html .= $this->addField('barcode-transfer-item',
            'text',
            array(
                'name' => 'barcode',
                'label' => $this->__('Barcode'),
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

    public function getLoadBarcodeUrl()
    {
        return $this->getUrl(
            'adminhtml/purchaseordersuccess_return_transferreditem/loadBarcode',
            array(
                'id' => $this->returnRequest->getReturnOrderId(),
                'supplier_id' => $this->returnRequest->getSupplierId()
            )
        );
    }

    public function getSubmitBarcodeUrl()
    {
        return $this->getUrl(
            'adminhtml/purchaseordersuccess_return_transferreditem/transfer',
            array(
                'id' => $this->returnRequest->getReturnOrderId(),
                'supplier_id' => $this->returnRequest->getSupplierId(),
                'modal' => 'scanbarcode'
            )
        );
    }
}