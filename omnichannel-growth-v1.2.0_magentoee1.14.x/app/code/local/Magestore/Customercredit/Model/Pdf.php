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
 * @package     Magestore_Storecredit
 * @module      Storecredit
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Class Magestore_Customercredit_Model_Pdf
 */
class Magestore_Customercredit_Model_Pdf extends Mage_Sales_Model_Order_Pdf_Total_Default
{

    /**
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $invoiceId = Mage::app()->getRequest()->getParam('invoice_id');
        $creditmemoId = Mage::app()->getRequest()->getParam('creditmemo_id');
        $fontSize = $this->getFontSize() ? $this->getFontSize() : $this->getDefaultFontSize();
        if ($invoiceId) {
            $invoice = Mage::getModel('sales/order_invoice')->load($invoiceId);
            $amount = $this->getOrder()->formatPriceTxt($invoice->getCustomercreditDiscount());
        } else if ($creditmemoId) {
            $creditmemo = Mage::getModel('sales/order_creditmemo')->load($creditmemoId);
            $amount = $this->getOrder()->formatPriceTxt($creditmemo->getCustomercreditDiscount());
        } else {
            $amount = $this->getOrder()->formatPriceTxt($this->getOrder()->getCustomercreditDiscount());
        }

        if ($this->getAmountPrefix()) {
            $amount = $this->getAmountPrefix() . $amount;
        }
        $total = array(array(
                'label' => 'Customer Credit',
                'amount' => '-' . $this->getAmountPrefix() . $amount,
                'font_size' => $fontSize,
        ));
        return $total;
    }

    /**
     * @return mixed
     */
    public function getDefaultFontSize(){
        return Mage::getStoreConfig('customercredit/style_management/default_font_size');
    }

}
