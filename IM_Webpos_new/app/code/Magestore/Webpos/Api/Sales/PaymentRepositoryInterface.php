<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Sales;

/**
 * Interface PaymentRepositoryInterface
 * @package Magestore\Webpos\Api\Sales
 */
interface PaymentRepositoryInterface
{
    /**
     * Add payment for order
     *
     * @param int $id The order ID.
     * @param \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface WebposOrder interface.
     */
    public function takePayment($id, \Magestore\Webpos\Api\Data\Checkout\PaymentInterface $payment);

}
