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
 * Interface Magestore_Webpos_Api_ShoppingCartInterface
 */
interface Magestore_Webpos_Api_ShoppingCartInterface
{
    /**#@+
     * Params key
     */
    const REMOVE_IDS = 'remove_ids';
    const MOVE_IDS = 'move_ids';
    /**#@- */

    /**#@+
     * Actions code
     */
    const ACTION_GET_ITEMS = 'get_shopping_cart_items';
    const ACTION_UPDATE_ITEMS = 'update_shopping_cart_items';
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
