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
 * Giftvoucher Creditmemo Totals Block
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Magestore_Giftvoucher_Block_Order_Creditmemo_Totals extends Mage_Core_Block_Template
{

    public function initTotals()
    {
        $orderTotalsBlock = $this->getParentBlock();
        $order = $orderTotalsBlock->getCreditmemo();
        if ($order->getGiftVoucherDiscount() && $order->getGiftVoucherDiscount() > 0) {
            $orderTotalsBlock->addTotal(new Varien_Object(array(
                'code' => 'giftvoucher',
                'label' => $this->__('Gift card (%s)', $order->getOrder()->getGiftCodes()),
                'value' => -$order->getGiftVoucherDiscount(),
                'base_value' => -$order->getBaseGiftVoucherDiscount(),
                )), 'subtotal');
        }
        if ($refund = $order->getGiftcardRefundAmount()) {
            if ($order->getOrder()->getCustomerIsGuest() 
                || !Mage::helper('giftvoucher')->getGeneralConfig('enablecredit', $order->getStoreId())) {
                $label = $this->__('Refund to your Gift Card code');
            } else {
                $label = $this->__('Refund to your credit balance');
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
