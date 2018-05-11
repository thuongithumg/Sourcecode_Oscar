<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Customer;

interface CustomerComplainRepositoryInterface
{

    /**
     * @param \Magestore\Webpos\Api\Data\Customer\CustomerComplainInterface $complain
     * @return string
     */
    public function save($complain);
    /**
     * Retrieve customers complain matching the specified criteria.
     *
     * @api
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Customer\CustomerComplainSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

}
