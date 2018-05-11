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
 * Interface Magestore_Webpos_Api_Checkout_Integration_ModuleInterface
 */
interface Magestore_Webpos_Api_Checkout_Integration_ModuleInterface
{
    /**#@+
     * Constants for field names
     */
    const KEY_MODULE = 'module';
    const KEY_ORDER_DATA = 'order_data';
    const KEY_EXTENSION_DATA = 'extension_data';
    const KEY_EVENT_NAME = 'event_name';
    /**#@-*/

    /**
     * Returns the module code.
     *
     * @return string module code. Otherwise, null.
     */
    public function getModule();

    /**
     * Sets the module code.
     *
     * @param string $module
     * @return $this
     */
    public function setModule($module);

    /**
     * Returns the module order data.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\ExtensionDataInterface[] Value. Otherwise, null.
     */
    public function getOrderData();

    /**
     * Sets the module order data.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\ExtensionDataInterface[] $orderData
     * @return $this
     */
    public function setOrderData($orderData);

    /**
     * Returns the module data.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\ExtensionDataInterface[] Value. Otherwise, null.
     */
    public function getExtensionData();

    /**
     * Sets the module  data.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\ExtensionDataInterface[] $extensionData
     * @return $this
     */
    public function setExtensionData($extensionData);

    /**
     * Returns event name.
     *
     * @return string event name. Otherwise, null.
     */
    public function getEventName();

    /**
     * Sets event name.
     *
     * @param string $eventName
     * @return $this
     */
    public function setEventName($eventName);

}
