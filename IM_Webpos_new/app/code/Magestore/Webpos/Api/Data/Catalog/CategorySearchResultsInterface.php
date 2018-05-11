<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Catalog;

/**
 * @api
 */
interface CategorySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get attributes list.
     *
     * @return \Magestore\Webpos\Api\Data\Catalog\CategoryInterface[]
     */
    public function getItems();

    /**
     * Set attributes list.
     *
     * @param \Magestore\Webpos\Api\Data\Catalog\CategoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * Get first categories
     *
     * @return string[]
     */
    public function getFirstCategories();

    /**
     * Set total count.
     *
     * @param string[] $categories
     * @return $this
     */
    public function setFirstCategories($categories);
}
