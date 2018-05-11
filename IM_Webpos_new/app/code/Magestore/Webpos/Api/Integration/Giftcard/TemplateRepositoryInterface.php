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
interface TemplateRepositoryInterface
{
    /**
     * Get template list
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Integration\Giftcard\TemplateResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
