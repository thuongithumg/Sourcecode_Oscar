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

define(
    [],
    function () {
        "use strict";

        /**
         * Main overlay z-index: 140
         */
        return {
            SESSION_KEY:'WEBPOSSESSION',
            MAIN_CONTAINER:'#checkout_container',
            ELEMENT_SELECTOR:{
                COL_LEFT:".col-left",
                MENU:"#c-menu--push-left",
                SHOW_MENU_BUTTON:".show-menu-button",
                NOTIFICATION_BUTTON:".notification-bell",
                MENU_MASK:"#c-mask",
                WRAPPER:"#o-wrapper",
                ACTIVE_CONTAINER:".pos_container.active",
                MAIN_OVERLAY:"#webpos-main-overlay",
                MEDIUM_OVERLAY:"#webpos-medium-overlay",
                EDIT_CART_ITEM_POPUP:"#popup-edit-product",
                EDIT_CART_ITEM_QTY_INPUT:"#editpopup_product_qty",
                CART_DISCOUNT_POPUP:"#webpos_cart_discountpopup",
                ARROW:".arrow",
                DYNAMIC_OVERLAY:'.pos-overlay.main',
                DYNAMIC_OVERLAY_ACTIVE:'.pos-overlay.main.active',
                CHECKOUT_SECTION:'#webpos_checkout',
                CART_ADDITIONAL_ACTIONS:'#cart-additional-actions',
                CART_COMMENT_POPUP:'#cart-add-comment-popup',
                CHECKOUT_ADD_PAYMENT_POPUP:'#add-more-payment',
                CHECKOUT_CREATE_INVOICE_BUTTON:'#can_paid',
                CHECKOUT_CREATE_SHIPMENT_BUTTON:'#can_ship',
                UI_SELECT_INPUT:'.ios-ui-select',
                NOTIFICATION_BELL:'.notification-bell'
            },
            CLASS:{
                ACTIVE:"active",
                POS_CONTAINER:"pos_container",
                HAS_ACTIVE_MENU:"has-active-menu",
                MENU_ACTIVE:"is-active",
                WRAPPER_MENU_ACTIVE:"has-push-left",
                SHOW_MENU:"showMenu",
                HIDE:"hide",
                SHOW:"show",
                ACTIVE_ON_CHECKOUT:"active-on-checkout",
                CHECKED:"checked"
            },
            EVENT:{
                SHOW_CONTAINER_AFTER:'show_container_after',
                DATA_MANAGER_SET_DATA_AFTER:'data_manager_set_data_after',
                LOGOUT_WITHOUT_CONFIRM:'logout_without_confirm',
                SHOW_POPUP_SELECT_CASH_DRAWER:'show_popup_select_cash_drawer',
                SELECT_CASH_DRAWER_AFTER:'select_cash_drawer_after',
                CLEAR_SESSION_AFTER:'clear_session_after',
                INIT_SHIFT_REPORT_DATA:'init_shift_report_data',
                PRINT_ZREPORT:'print_zreport',
                CLOSE_SHIFT_AFTER:'close_shift_after',
                OPEN_SHIFT_AFTER:'open_shift_after',
                REFUND_CASH_AFTER:'refund_cash_after'
            },
            CONFIG_PATH:{
                IS_USE_ONLINE:'os_checkout/enable_online_mode',
                AUTO_CHECK_PROMOTION:'os_checkout/auto_check_promotion_rules',
                AUTO_SYNC_POINTS_BALANCE:'os_reward_points/auto_sync_balance_when_checkout',
                AUTO_SYNC_CREDIT_BALANCE:'os_store_credit/auto_sync_balance_when_checkout',
                SHOW_CUSTOMER_POINTS_BALANCE_ON_RECEIPT:'os_reward_points/show_customer_points_balance_on_receipt',
                SHOW_CUSTOMER_CREDIT_BALANCE_ON_RECEIPT:'os_store_credit/show_customer_credit_balance_on_receipt',
                PDF_INVOICE_PLUS_ORDERS_PRINTING_TEMPLATE:'os_pdf_invoice_plus/pdf_orders_printing_template'
            }
        };
    }
);