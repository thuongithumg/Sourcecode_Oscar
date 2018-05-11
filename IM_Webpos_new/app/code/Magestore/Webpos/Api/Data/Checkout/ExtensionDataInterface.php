<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Checkout;


/**
 * Interface ExtensionDataInterface
 * @package Magestore\Webpos\Api\Data\Checkout
 */
interface ExtensionDataInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for field names
     */
    const KEY_FIELD_KEY = 'key';
    const KEY_FIELD_VALUE = 'value';
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

}
