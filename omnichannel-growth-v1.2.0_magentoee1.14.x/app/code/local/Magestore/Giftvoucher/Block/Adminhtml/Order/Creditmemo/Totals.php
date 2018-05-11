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
 * Class Magestore_Giftvoucher_Block_Adminhtml_Order_Creditmemo_Totals
 */
class Magestore_Giftvoucher_Block_Adminhtml_Order_Creditmemo_Totals extends Mage_Adminhtml_Block_Sales_Order_Totals_Item {

    /**
     *
     */
    public function initTotals() {
        $orderTotalsBlock = $this->getParentBlock();
        $order = $orderTotalsBlock->getCreditmemo();
        if ($order->getGiftVoucherDiscount() && $order->getGiftVoucherDiscount() > 0) {
            $orderTotalsBlock->addTotal(new Varien_Object(array(
                'code' => 'giftvoucher',
                'label' => $this->__('Gift Card (%s)', $order->getOrder()->getGiftCodes()),
                'value' => -$order->getGiftVoucherDiscount(),
                'base_value' => -$order->getBaseGiftVoucherDiscount(),
                    )), 'subtotal');
        }
        if ($refund = $order->getGiftcardRefundAmount()) {
            if ($order->getOrder()->getCustomerIsGuest() || !Mage::helper('giftvoucher')->getGeneralConfig('enablecredit', $order->getStoreId())) {
                $label = $this->__('Refund to customer gift card code used to check out');
            } else {
                $label = $this->__('Refund to customer\'s Gift Card credit balance');
            }
            $orderTotalsBlock->addTotal(new Varien_Object(array(
                'code' => 'giftcard_refund',
                'label' => $label,
                'value' => $refund,
                'area' => 'footer',
                    )), 'subtotal');
        }
    }

}