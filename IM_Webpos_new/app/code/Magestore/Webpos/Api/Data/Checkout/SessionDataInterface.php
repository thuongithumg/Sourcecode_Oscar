<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Checkout;


/**
 * Interface SessionDataInterface
 * @package Magestore\Webpos\Api\Data\Checkout
 */
interface SessionDataInterface extends \Magento\Framework\Api\ExtensibleDataInterface
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
