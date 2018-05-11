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
 * Interface Magestore_Webpos_Api_Integration_GiftcardInterface
 */
interface Magestore_Webpos_Api_Integration_GiftcardInterface extends Magestore_Webpos_Api_CheckoutInterface
{
    /**#@+
     * Params key
     */
    const AMOUNT = 'amount';
    const BASE_AMOUNT = 'base_amount';
    /**#@- */

    /**#@+
     * Actions code
     */
    const ACTION_REFUND_BALANCE = 'refund_giftcard_balance';
    const ACTION_GET_BALANCE = 'get_giftcard_balance';
    const ACTION_APPLY_GIFTCARD = 'apply_gift_card';
    const ACTION_REMOVE_GIFTCARD = 'remove_gift_card';
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
