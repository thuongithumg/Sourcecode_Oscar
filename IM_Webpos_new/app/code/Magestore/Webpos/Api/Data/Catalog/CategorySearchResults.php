<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Catalog;

/**
 * SearchResults Service Data Object used for the search service requests
 */
class CategorySearchResults extends \Magento\Framework\Api\SearchResults
{
    /**
     * Get first categories
     *
     * @return string[]
     */
    public function getFirstCategories()
    {
        return $this->_get('first_categories') === null ? [] : $this->_get('first_categories');
    }

    /**
     * Set total count.
     *
     * @param string[] $categories
     * @return $this
     */
    public function setFirstCategories($categories)
    {
        return $this->setData('first_categories', $categories);
    }
}
