<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Integration\Giftcard;

/**
 * Interface GiftcardRepositoryInterface
 * @package Magestore\Webpos\Api\Integration\Giftcard
 */
interface GiftcardRepositoryInterface
{
    /**
     * @param string $orderId
     * @param string $amount
     * @param string $baseAmount
     * @return string
     */
    public function refundGiftcardCode($orderId, $amount, $baseAmount);
}
