<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Integration\Rewardpoints;

/**
 * @api
 */
interface RateSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get attributes list.
     *
     * @return \Magestore\Webpos\Api\Data\Integration\Rewardpoints\RateInterface[]
     */
    public function getItems();
}
