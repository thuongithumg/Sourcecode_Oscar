<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Integration\Rewardpoints;

/**
 * Interface LocationRepository
 * @api
 */
interface RateRepositoryInterface
{
    /**
     * Get Current Location
     *
     * @return \Magestore\Webpos\Api\Data\Integration\Rewardpoints\RateSearchResultsInterface
     */
    public function getList();
}