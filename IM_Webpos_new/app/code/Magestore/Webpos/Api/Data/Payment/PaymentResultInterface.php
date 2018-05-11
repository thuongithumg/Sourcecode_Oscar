<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Payment;

interface PaymentResultInterface
{
    /**
     * Set payments list.
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Payment\PaymentInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * Get payments list.
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Payment\PaymentInterface[]
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
