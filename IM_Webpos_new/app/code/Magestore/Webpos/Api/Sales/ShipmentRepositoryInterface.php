<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Sales;

interface ShipmentRepositoryInterface extends \Magento\Sales\Api\ShipmentRepositoryInterface
{
    /**
     * Performs persist operations for a specified shipment.
     *
     * @param \Magento\Sales\Api\Data\ShipmentInterface $entity The shipment.
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface Order interface.
     */
    public function saveShipment(\Magento\Sales\Api\Data\ShipmentInterface $entity);

}
