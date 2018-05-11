<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Integration\Rewardpoints;

/**
 * Interface PointRepositoryInterface
 * @package Magestore\Webpos\Api\Integration\Rewardpoints
 */
interface PointRepositoryInterface
{
    /**
     * @param string $customerId
     * @return string
     */
    public function getBalance($customerId);

    /**
     *
     * @return \Magestore\Webpos\Api\Data\Integration\Rewardpoints\SearchResultsInterface
     */
    public function getList();
}
