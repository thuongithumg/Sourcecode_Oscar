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
interface CustomerCreditSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get customer complain list.
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Customer\CustomerCreditInterface[]
     */
    public function getItems();

    /**
     * Set customer credit list.
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Customer\CustomerCreditInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
