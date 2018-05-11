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
var arrayBlock = [
    'require',
    'ui/components/base/abstract',
    // 'view/catalog/product/detail-popup',
    'ui/components/order/list',
    'ui/components/order/detail',
    // 'view/sales/order/view/payment',
    'ui/components/order/action',
    // 'view/sales/order/sendemail',
    // 'view/sales/order/comment',
    // 'view/sales/order/invoice',
    // 'view/sales/order/shipment',
    // 'view/sales/order/creditmemo',
    // 'view/sales/order/cancel',
    // 'view/checkout/cart/discountpopup',
    // 'view/checkout/customer/add-billing-address',
    // 'view/checkout/cart',
    // 'view/checkout/customer/edit-customer',
    // 'view/container',
    // 'view/catalog/product-list',
    // 'view/sales/order/hold-view',
    // 'view/checkout/checkout/renderer/payment',
    // 'view/shift/cash-transaction/activity',
    // 'view/catalog/category/breadcrumbs',
    // 'view/checkout/checkout/payment_popup',
    // 'view/checkout/checkout/shipping',
    // 'view/checkout/checkout/payment',
    // 'view/checkout/checkout/payment_selected',
    // 'view/checkout/checkout/payment_creditcard',
    // 'view/checkout/checkout/swipe/jquery.cardswipe',
    // 'view/checkout/customer/add-shipping-address',
    // 'view/checkout/checkout/receipt',
    // 'view/customer/customer-view',
    // 'view/catalog/category/cell-grid',
    // 'view/checkout/customer/add-customer',
    'ui/components/session/session/session-listing',
    'ui/components/session/session/session-detail',
    'ui/components/session/sales-summary/sales-summary',
    'ui/components/session/sales-summary/zreport',
    'ui/components/session/cash-transaction/cash-adjustment',
    'ui/components/session/cash-transaction/activity',
    'ui/components/session/session/open-session',
    'ui/components/session/session/close-session'
];

define(
    arrayBlock
    ,
    function (require,
              view_base_abstract,
              // view_catalog_product_detailpopup,
              view_sales_order_list,
              view_sales_order_view,
              // view_sales_order_view_payment,
              view_sales_order_action,
              // view_sales_order_sendemail,
              // view_sales_order_comment,
              // view_sales_order_invoice,
              // view_sales_order_shipment,
              // view_sales_order_creditmemo,
              // view_sales_order_cancel,
              // view_checkout_cart_discountpopup,
              // view_checkout_customer_addbillingaddress,
              // view_checkout_cart,
              // view_checkout_customer_editcustomer,
              // view_container,
              // view_catalog_productlist,
              // view_sales_order_holdview,
              // view_checkout_checkout_renderer_payment,
              // view_shift_cashtransaction_activity,
              // view_catalog_category_breadcrumbs,
              // view_checkout_checkout_payment_popup,
              // view_checkout_checkout_shipping,
              // view_checkout_checkout_payment,
              // view_checkout_checkout_payment_selected,
              // view_checkout_checkout_payment_creditcard,
              // view_checkout_checkout_swipe_jquerycardswipe,
              // view_checkout_customer_addshippingaddress,
              // view_checkout_checkout_receipt,
              // view_customer_customerview,
              // view_catalog_category_cellgrid,
              // view_checkout_customer_addcustomer,
              view_session_list,
              view_session_detail,
              view_session_detail_salessummary_salessummary,
              view_session_detail_salessummary_zreport,
              view_session_detail_cashtransaction_cashadjustment,
              view_session_detail_cashtransaction_activity,
              view_session_detail_openshift,
              view_session_detail_closeshift
    ) {
        "use strict";

        return {
            getSingleton: function (viewName) {
                var view = this._convertModelPath(viewName);
                if(!window.webposViews) {
                    window.webposViews = {};
                } 
                if(!window.webposViews[view]) {
                     var viewClass = require(''+viewName);
                     window.webposViews[view] = viewClass();
                }
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