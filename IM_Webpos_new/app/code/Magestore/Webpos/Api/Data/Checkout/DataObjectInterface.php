<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Checkout;


/**
 * Interface DataObjectInterface
 * @package Magestore\Webpos\Api\Data\Checkout
 */
interface DataObjectInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for field names
     */
    const KEY_ID = 'id';
    const KEY_VALUE = 'value';
    /**#@-*/
    
    /**
     * Returns id.
     *
     * @return string id. Otherwise, null.
     */
    public function getId();

    /**
     * Sets id.
     *
     * @param string $id
     * @return $this
     */
    public function setId($id);
    
    /**
     * Returns the value.
     *
     * @return string Value. Otherwise, null.
     */
    public function getValue();

    /**
     * Sets the value.
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value);

}
