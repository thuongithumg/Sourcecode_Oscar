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
 * Interface Magestore_Webpos_Api_TransactionInterface
 */
interface Magestore_Webpos_Api_TransactionInterface
{
    /**#@+
     * Params key
     */
    const DATA = 'data';

    const ID = 'id';
    const TILL_ID = 'till_id';
    const SHIFT_ID = 'shift_id';
    const STAFF_ID = 'staff_id';
    const STAFf_NAME = 'staff_name';
    const ORDER_INCREMENT_ID = 'order_increment_id';
    const AMOUNT = 'amount';
    const BASE_AMOUNT = 'base_amount';
    const TRANSACTION_CURRENCY_CODE = 'transaction_currency_code';
    const BASE_CURRENCY_CODE = 'base_currency_code';
    const NOTE = 'note';
    const CREATED_AT = 'created_at';
    const IS_MANUAL = 'is_manual';
    const IS_OPENING = 'is_opening';
    const VALUE = 'value';
    const BASE_VALUE = 'base_value';
    const BALANCE = 'balance';
    const BASE_BALANCE = 'base_balance';
    const TYPE = 'type';
    /**#@- */

    /**#@+
     * Actions code
     */
    const ACTION_SAVE_TRANSACTION = 'save_transaction';
    const ACTION_APP_SAVE_TRANSACTION = 'app_save_transaction';
    const ACTION_GET_LIST_TRANSACTION = 'get_list_transaction';
    /**#@- */

    /**#@+
     * Scope code
     */

    /**#@- */

    /**#@+
     * Data model
     */

    /**#@- */

    /**#@+
     * Event
     */
    /**#@- */

    /**#@+
     * Message
     */

    /**#@- */
}
