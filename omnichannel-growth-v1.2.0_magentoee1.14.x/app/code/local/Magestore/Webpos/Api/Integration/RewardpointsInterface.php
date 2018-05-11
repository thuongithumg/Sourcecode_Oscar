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
 * Interface Magestore_Webpos_Api_Integration_RewardpointsInterface
 */
interface Magestore_Webpos_Api_Integration_RewardpointsInterface extends Magestore_Webpos_Api_CheckoutInterface
{
    /**#@+
     * Params key
     */
    const CUSTOMER_ID = 'customer_id';
    const DATA = 'data'; // {use_point:'', rule_id:'}
    const USE_POINT = 'use_point';
    const RULE_ID = 'rule_id';
    /**#@- */

    /**#@+
     * Actions code
     */
    const ACTION_GET_RATES = 'get_rewardpoints_rates';
    const ACTION_GET_BALANCE = 'get_points_balance';
    const ACTION_GET_LIST = 'get_points_list';
    const ACTION_SPEND_POINT = 'spend_point';
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
