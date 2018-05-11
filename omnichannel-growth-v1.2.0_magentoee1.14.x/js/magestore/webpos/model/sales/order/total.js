/*
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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

define([
    'jquery',
    'mage/translate'
], function ($, $t) {
    'use strict';
    return {
        getTotalOrderView: function(){
            return [
                {totalName: 'base_subtotal', totalLabel: $t('Subtotal'), required: true, isPrice:true},
                {totalName: 'rewardpoints_earn', totalLabel: $t('Earned Points'), required: false, isPrice:false, valueLabel:('Points') },
                {totalName: 'rewardpoints_spent', totalLabel: $t('Spent Points'), required: false, isPrice:false, valueLabel:('Points')},
                {totalName: 'base_shipping_amount', totalLabel: $t('Shipping'), required: true, isPrice:true},
                {totalName: 'base_tax_amount', totalLabel: $t('Tax'), required: false, isPrice:true},
                {totalName: 'base_discount_amount', totalLabel: $t('Discount'), required: false, isPrice:true},
                {totalName: 'base_gift_voucher_discount', totalLabel: $t('Gift Card'), required: false, isPrice:true},
                {totalName: 'rewardpoints_base_discount', totalLabel: $t('Points Discount'), required: false, isPrice:true},
                {totalName: 'base_grand_total', totalLabel: $t('Grand Total'), required: true, isPrice:true},
                {totalName: 'base_total_paid', totalLabel: $t('Total Paid'), required: true, isPrice:true},
                {totalName: 'base_total_refunded', totalLabel: $t('Total Refunded'), required: false, isPrice:true},
                {totalName: 'webpos_base_change', totalLabel: $t('Change'), required: false, isPrice:true},
            ]
        },

        getTotalOrderHold: function(){
            return [
                {totalName: 'base_subtotal', totalLabel: $t('Subtotal'), required: true},
                {totalName: 'base_shipping_amount', totalLabel: $t('Shipping'), required: true},
                {totalName: 'base_tax_amount', totalLabel: $t('Tax'), required: false},
                {totalName: 'base_discount_amount', totalLabel: $t('Discount'), required: false},
                {totalName: 'base_grand_total', totalLabel: $t('Grand Total'), required: true}
            ]
        },
        
        getTotalOrderPrint: function(){
            return this.getTotalOrderView();
        }
    }
});
