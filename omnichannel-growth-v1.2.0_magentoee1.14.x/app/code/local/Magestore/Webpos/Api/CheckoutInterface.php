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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Interface Magestore_Webpos_Api_CheckoutInterface
 */
interface Magestore_Webpos_Api_CheckoutInterface
{
    /**#@+
     * Params key
     */
    const STORE = 'store';

    const CHECKOUT_CUSTOMER_ID = 'customer_id';
    const CHECKOUT_ITEMS = 'items';
    const CHECKOUT_PAYMENT = 'payment';
    const CHECKOUT_SHIPPING = 'shipping';
    const CHECKOUT_CONFIG = 'config';
    const CHECKOUT_COUPON_CODE = 'coupon_code';
    const CHECKOUT_EXTENSION_DATA = 'extension_data';
    const CHECKOUT_SESSION_DATA = 'session_data';
    const CHECKOUT_INTEGRATION = 'integration';

    const ITEM_ID = 'item_id';
    const SECTION = 'section';
    const SHIPPING_METHOD = 'shipping_method';
    const PAYMENT_METHOD = 'payment_method';
    const CUSTOMER = 'customer';
    const QUOTE_DATA = 'quote_data';
    const ORDER_INCREMENT_ID = 'increment_id';
    const EMAIL = 'email';
    const ACTIONS = 'actions';
    /**#@- */

    /**#@+
     * Actions code
     */
    const ACTION_GET_CART_DATA = 'get_cart_data';
    const ACTION_SAVE_CART = 'save_cart';
    const ACTION_REMOVE_CART = 'remove_cart';
    const ACTION_REMOVE_ITEM = 'remove_item';
    const ACTION_SAVE_SHIPPING_METHOD = 'save_shipping_method';
    const ACTION_SAVE_PAYMENT_METHOD = 'save_payment_method';
    const ACTION_PLACE_ORDER = 'place_order';
    const ACTION_SYNC_ORDER = 'sync_order';
    const ACTION_CANCEL_COUPON = 'cancel_coupon';
    const ACTION_APPLY_COUPON = 'apply_coupon';
    const ACTION_SELECT_CUSTOMER = 'select_customer';
    const ACTION_SAVE_QUOTE_DATA = 'save_quote_data';
    const ACTION_CHECK_PROMOTION = 'check_promotion';
    const ACTION_SEND_ORDER_EMAIL = 'send_order_email';
    /**#@- */

    /**#@+
     * Scope code
     */
    const SCOPE_CART = 'cart';
    const SCOPE_CHECKOUT = 'checkout';
    /**#@- */

    /**#@+
     * Data model
     */
    const DATA_INTEGRATION = 'integration_module';
    const DATA_PAYMENT = 'payment';
    const DATA_SHIPPING = 'shipping';
    const DATA_SHIPPING_TRACK = 'shippingTrack';
    const DATA_PAYMENT_ITEM = 'paymentItem';
    const DATA_ADDRESS = 'address';
    const DATA_CONFIG = 'config';
    const DATA_ITEM_REQUEST = 'itemRequest';
    const DATA_QUOTE_INIT = 'quoteDataInit';
    /**#@- */

    /**#@+
     * Event
     */
    const EVENT_WEBPOS_EMPTY_CART_BEFORE = 'webpos_empty_cart_before';
    const EVENT_WEBPOS_EMPTY_CART_AFTER = 'webpos_empty_cart_after';
    const EVENT_WEBPOS_SAVE_CART_AFTER = 'webpos_save_cart_after';
    const EVENT_WEBPOS_SEND_RESPONSE_BEFORE = 'webpos_send_response_before';
    const EVENT_WEBPOS_PLACE_ORDER_AFTER = 'webpos_place_order_after';
    const EVENT_WEBPOS_GET_PAYMENT_AFTER = 'webpos_get_payment_after';
    const EVENT_WEBPOS_GET_SHIPPINGS_AFTER = 'webpos_get_shippings_after';
    const EVENT_WEBPOS_GET_TOTALS_AFTER = 'webpos_get_totals_after';
    /**#@- */

    /**#@+
     * Message
     */

    /**#@- */
}
