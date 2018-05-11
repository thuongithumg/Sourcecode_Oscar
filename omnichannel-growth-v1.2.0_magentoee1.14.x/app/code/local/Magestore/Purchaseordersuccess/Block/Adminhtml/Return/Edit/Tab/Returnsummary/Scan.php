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
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Status as PurchaseorderStatus;

class Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Returnsummary_Scan
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Barcode_ReturnScan
{
    protected $_template = 'purchaseordersuccess/return/edit/tab/return_summary/scan.phtml';

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->returnRequest->canAddProduct())
            return parent::_toHtml();
        else
            return '';
    }

}