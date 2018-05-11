<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Catalog;

/**
 * @api
 */
interface ProductOriginalSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get attributes list.
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\ProductOriginalInterface[]
     */
    public function getItems();

    /**
     * Set attributes list.
     *
     * @param \Magestore\Webpos\Api\Data\Catalog\ProductOriginalInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
