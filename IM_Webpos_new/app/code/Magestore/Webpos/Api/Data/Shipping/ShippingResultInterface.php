<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Shipping;

interface ShippingResultInterface
{
    /**
     * Set shipping methods list.
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Shipping\ShippingInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * Get shipping methods list.
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Shipping\ShippingInterface[]
     */
    public function getItems();

    /**
     * Set total count
     *
     * @param int $count
     * @return $this
     */
    public function setTotalCount($count);

    /**
     * Get total count
     *
     * @return int
     */
    public function getTotalCount();

}
