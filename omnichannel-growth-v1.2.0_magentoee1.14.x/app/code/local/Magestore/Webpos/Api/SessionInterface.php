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
 * Interface Magestore_Webpos_Api_ShiftInterface
 */
interface Magestore_Webpos_Api_SessionInterface
{
    /**#@+
     * Params key
     */
    const DATA = 'data';
    const TILL_ID = 'till_id';

    /**#@- */

    /**#@+
     * Actions code
     */
    const ACTION_GET_DATA = 'get_session_data';
    const ACTION_CLOSE_SESSION = 'close_session';
    const ACTION_GET_LIST = 'get_session_list';
    const ACTION_SAVE = 'save';
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
