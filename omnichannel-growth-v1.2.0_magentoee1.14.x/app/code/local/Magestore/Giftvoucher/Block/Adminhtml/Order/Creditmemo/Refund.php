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
 * @package     Magestore_Giftvoucher
 * @module     Giftvoucher
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Giftvoucher_Block_Adminhtml_Order_Creditmemo_Refund
 */
class Magestore_Giftvoucher_Block_Adminhtml_Order_Creditmemo_Refund extends Mage_Adminhtml_Block_Template {

    /**
     * @return mixed
     */
    public function getCreditmemo() {
        return Mage::registry('current_creditmemo');
    }

    /**
     * @return mixed
     */
    public function getOrder() {
        return $this->getCreditmemo()->getOrder();
    }

    /**
     * @return bool|Mage_Core_Model_Abstract
     */
    public function getCustomer() {
        $order = $this->getOrder();
        if ($order->getCustomerIsGuest()) {
            return false;
        }
        $customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
        if ($customer->getId()) {
            return $customer;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function getIsShow() {
        return ($this->getCreditmemo()->getUseGiftCreditAmount() || $this->getCreditmemo()->getGiftVoucherDiscount());
    }

    /**
     * @return float
     */
    public function getMaxAmount() {
        $maxAmount = 0;
        if ($this->getCreditmemo()->getUseGiftCreditAmount() && Mage::helper('giftvoucher')->getGeneralConfig('enablecredit', $this->getOrder()->getStoreId())) {
            $maxAmount += floatval($this->getCreditmemo()->getUseGiftCreditAmount());
        }
        if ($this->getCreditmemo()->getGiftVoucherDiscount()) {
            $maxAmount += floatval($this->getCreditmemo()->getGiftVoucherDiscount());
        }
        return Mage::app()->getStore($this->getOrder()->getStoreId())->roundPrice($maxAmount);
    }

    /**
     * @param $price
     * @return mixed
     */
    public function formatPrice($price) {
        return $this->getOrder()->formatPrice($price);
    }

}
