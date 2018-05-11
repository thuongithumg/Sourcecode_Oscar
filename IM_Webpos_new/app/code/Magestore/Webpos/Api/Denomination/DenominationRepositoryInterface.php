<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Denomination;


/**
 * Interface DenominationRepositoryInterface
 * @package Magestore\Webpos\Api\Pos
 */
interface DenominationRepositoryInterface
{
    /**
     * get list Pos
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Denomination\DenominationSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria);


}