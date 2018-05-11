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
 * Interface Magestore_Webpos_Api_Integration_StorecreditInterface
 */
interface Magestore_Webpos_Api_Integration_StorecreditInterface extends Magestore_Webpos_Api_CheckoutInterface
{
    /**#@+
     * Params key
     */
    const CUSTOMER_ID = 'customer_id';
    const AMOUNT = 'amount';
    /**#@- */

    /**#@+
     * Actions code
     */
    const ACTION_GET_LIST = 'get_credit_list';
    const ACTION_GET_BALANCE = 'get_credit_balance';
    const ACTION_REFUND_BY_CREDIT = 'refund_by_credit';
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
     * Message
     */

    /**#@- */
}
