<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Staff;

/**
 * @api
 */
interface StaffSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return \Magestore\Webpos\Api\Data\Staff\StaffListDataInterface[] Array of collection items
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param \Magestore\Webpos\Api\Data\Staff\StaffListDataInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
