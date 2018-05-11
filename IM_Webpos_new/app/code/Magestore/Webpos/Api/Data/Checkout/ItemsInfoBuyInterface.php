<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Checkout;


/**
 * Interface ItemsInfoBuyInterface
 * @package Magestore\Webpos\Api\Data\Checkout
 */
interface ItemsInfoBuyInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for field names
     */
    const KEY_ITEMS = 'items';
    /**#@-*/
    
    /**
     * Returns the order items.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\InfoBuyInterface[].
     */
    public function getItems();

    /**
     * Sets the order items.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\InfoBuyInterface[] $items
     * @return $this
     */
    public function setItems($items);

}
