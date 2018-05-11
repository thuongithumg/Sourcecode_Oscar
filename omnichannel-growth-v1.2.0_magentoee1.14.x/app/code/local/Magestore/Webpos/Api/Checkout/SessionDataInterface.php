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
 * Interface Magestore_Webpos_Api_Checkout_SessionDataInterface
 */
interface Magestore_Webpos_Api_Checkout_SessionDataInterface
{
    /**#@+
     * Constants for field names
     */
    const KEY_FIELD_KEY = 'key';
    const KEY_FIELD_VALUE = 'value';
    const KEY_SESSION_CLASS = 'class';
    /**#@-*/

    /**
     * Returns the field key.
     *
     * @return string field key. Otherwise, null.
     */
    public function getKey();

    /**
     * Sets the field key.
     *
     * @param string $key
     * @return $this
     */
    public function setKey($key);

    /**
     * Returns the field value.
     *
     * @return string field value. Otherwise, null.
     */
    public function getValue();

    /**
     * Sets the field value.
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value);

    /**
     * Returns the model class name.
     *
     * @return string field value. Otherwise, null.
     */
    public function getClass();

    /**
     * Sets the model class name.
     *
     * @param string $class
     * @return $this
     */
    public function setClass($class);

}
