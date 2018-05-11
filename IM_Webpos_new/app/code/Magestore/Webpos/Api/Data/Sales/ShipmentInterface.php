<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Sales;

interface ShipmentInterface
{
    const ITEMS = 'items';

    /**
     * Gets items for the shipment.
     *
     * @return \Magestore\Webpos\Api\Data\Sales\ShipmmentInterface[] Array of items.
     */
    public function getItems();

    /**
     * Sets items for the shipment.
     *
     * @param \Magestore\Webpos\Api\Data\Sales\ShipmmentInterface[] $items
     * @return $this
     */
    public function setItems($items);
}
