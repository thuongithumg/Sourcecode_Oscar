/*
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
var arrayBlock = [
    'require',
    'Magestore_Webpos/js/view/base/abstract',
    'Magestore_Webpos/js/view/catalog/product/detail-popup',
    'Magestore_Webpos/js/view/sales/order/list',
    'Magestore_Webpos/js/view/sales/order/view',
    'Magestore_Webpos/js/view/sales/order/view/payment',
    'Magestore_Webpos/js/view/sales/order/action',
    'Magestore_Webpos/js/view/sales/order/sendemail',
    'Magestore_Webpos/js/view/sales/order/comment',
    'Magestore_Webpos/js/view/sales/order/invoice',
    'Magestore_Webpos/js/view/sales/order/shipment',
    'Magestore_Webpos/js/view/sales/order/creditmemo',
    'Magestore_Webpos/js/view/sales/order/cancel',
    'Magestore_Webpos/js/view/checkout/cart/discountpopup',
    'Magestore_Webpos/js/view/checkout/customer/add-billing-address',
    'Magestore_Webpos/js/view/checkout/cart',
    'Magestore_Webpos/js/view/checkout/customer/edit-customer',
    'Magestore_Webpos/js/view/container',
    'Magestore_Webpos/js/view/catalog/product-list',
    'Magestore_Webpos/js/view/sales/order/hold-view',
    'Magestore_Webpos/js/view/checkout/checkout/renderer/payment',
    'Magestore_Webpos/js/view/shift/cash-transaction/activity',
    'Magestore_Webpos/js/view/catalog/category/breadcrumbs',
    'Magestore_Webpos/js/view/checkout/checkout/payment_popup',
    'Magestore_Webpos/js/view/checkout/checkout/shipping',
    'Magestore_Webpos/js/view/checkout/checkout/payment',
    'Magestore_Webpos/js/view/checkout/checkout/payment_selected',
    'Magestore_Webpos/js/view/checkout/checkout/payment_creditcard',
    'Magestore_Webpos/js/view/checkout/checkout/swipe/jquery.cardswipe',
    'Magestore_Webpos/js/view/checkout/customer/add-shipping-address',
    'Magestore_Webpos/js/view/checkout/checkout/receipt',
    'Magestore_Webpos/js/view/customer/customer-view',
    'Magestore_Webpos/js/view/catalog/category/cell-grid',
    'Magestore_Webpos/js/view/checkout/customer/add-customer',
    'Magestore_Webpos/js/view/shift/shift/shift-listing',
    'Magestore_Webpos/js/view/shift/shift/shift-detail',
    'Magestore_Webpos/js/view/shift/sales-summary/sales-summary',
    'Magestore_Webpos/js/view/shift/sales-summary/zreport',
    'Magestore_Webpos/js/view/shift/cash-transaction/cash-adjustment',
    'Magestore_Webpos/js/view/shift/shift/close-shift'
];

define(
    arrayBlock
    ,
    function (require,
              view_base_abstract,
              view_catalog_product_detailpopup,
              view_sales_order_list,
              view_sales_order_view,
              view_sales_order_view_payment,
              view_sales_order_action,
              view_sales_order_sendemail,
              view_sales_order_comment,
              view_sales_order_invoice,
              view_sales_order_shipment,
              view_sales_order_creditmemo,
              view_sales_order_cancel,
              view_checkout_cart_discountpopup,
              view_checkout_customer_addbillingaddress,
              view_checkout_cart,
              view_checkout_customer_editcustomer,
              view_container,
              view_catalog_productlist,
              view_sales_order_holdview,
              view_checkout_checkout_renderer_payment,
              view_shift_cashtransaction_activity,
              view_catalog_category_breadcrumbs,
              view_checkout_checkout_payment_popup,
              view_checkout_checkout_shipping,
              view_checkout_checkout_payment,
              view_checkout_checkout_payment_selected,
              view_checkout_checkout_payment_creditcard,
              view_checkout_checkout_swipe_jquerycardswipe,
              view_checkout_customer_addshippingaddress,
              view_checkout_checkout_receipt,
              view_customer_customerview,
              view_catalog_category_cellgrid,
              view_checkout_customer_addcustomer,
              view_shift_shift_shiftlisting,
              view_shift_shift_shiftdetail,
              view_shift_salessummary_salessummary,
              view_shift_salessummary_zreport,
              view_shift_cashtransaction_cashadjustment,
              view_shift_shift_closeshift
    ) {
        "use strict";

        return {

            getSingleton: function (viewName) {
                var view = this._convertModelPath(viewName);
                if(!window.webposViews) {
                    window.webposViews = {};
                } 
                if(!window.webposViews[view]) {
                     var viewClass = require('Magestore_Webpos/js/'+viewName);
                     window.webposViews[view] = viewClass();
                }
                return window.webposViews[view];                
            },
            
            create: function (viewName) {
                var view = this._convertModelPath(viewName);
                if(!window.webposViews) {
                    window.webposViews = {};
                }        
                var viewClass = require('Magestore_Webpos/js/'+viewName);
                window.webposViews[view] = viewClass();
                return window.webposViews[view];
            },

            /**
             * convert model name to key
             *
             * @param string modelName
             * @returns string
             */
            _convertModelPath: function (modelName) {
                modelName = modelName.replace(/\//gi, '_');
                modelName = modelName.replace(/-/gi, '');
                modelName = modelName.replace(/\./gi, '');
                return modelName;
            }
        };
    }
);