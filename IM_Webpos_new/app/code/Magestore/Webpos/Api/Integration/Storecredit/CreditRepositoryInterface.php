<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Integration\Storecredit;

/**
 * Interface CreditRepositoryInterface
 * @package Magestore\Webpos\Api\Integration\Storecredit
 */
interface CreditRepositoryInterface
{
    /**
     * @param string $customerId
     * @return string
     */
    public function getBalance($customerId);

    /**
     * @param string $orderId
     * @param string $orderIncrementId
     * @param string $customerId
     * @param string $amount
     * @return string
     */
    public function refundByCredit($orderId, $orderIncrementId, $customerId, $amount);

    /**
     *
     * @return \Magestore\Webpos\Api\Data\Integration\Storecredit\SearchResultsInterface
     */
    public function getList();
}
