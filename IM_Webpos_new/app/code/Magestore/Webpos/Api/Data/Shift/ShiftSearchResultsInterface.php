<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Shift;

/**
 * @api
 */
interface ShiftSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return \Magestore\Webpos\Api\Data\Shift\ShiftInterface[] Array of collection items
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param \Magestore\Webpos\Api\Data\Shift\ShiftInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
