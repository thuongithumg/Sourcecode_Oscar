<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Sales;

interface OrderSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get items.
     *
     * @return \Magestore\Webpos\Api\Data\Sales\OrderInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set items.
     *
     * @param \Magestore\Webpos\Api\Data\Sales\OrderInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
