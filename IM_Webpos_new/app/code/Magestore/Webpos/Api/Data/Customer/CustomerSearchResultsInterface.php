<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Customer;

/**
 * Interface for customer search results.
 */
interface CustomerSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get customers list.
     *
     * @api
     * @return \Magestore\Webpos\Api\Data\Customer\CustomerInterface[]
     */
    public function getItems();

    /**
     * Set customers list.
     *
     * @api
     * @param \Magestore\Webpos\Api\Data\Customer\CustomerInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
