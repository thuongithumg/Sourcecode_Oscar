<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Checkout;


/**
 * Interface CartItemInterface
 * @package Magestore\Webpos\Api\Data\Checkout
 */
interface CartItemOptionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for field names
     */
    const KEY_CODE = 'code';
    const KEY_VALUE = 'value';
    /**#@-*/
    
    /**
     * Returns the option code.
     *
     * @return string option code. Otherwise, null.
     */
    public function getCode();

    /**
     * Sets the option code.
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code);
    
    /**
     * Returns the option value.
     *
     * @return string Value. Otherwise, null.
     */
    public function getValue();

    /**
     * Sets the option value.
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value);

}
