<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Customer;

/**
 * Interface AddressSearchResultsInterface
 * @package Magestore\Webpos\Api\Data
 */
interface CustomerComplainSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get customer complain list.
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Customer\CustomerComplainInterface[]
     */
    public function getItems();

    /**
     * Set customer complain list.
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Customer\CustomerComplainInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
