<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\InventorySuccess\Plugin\Bundle\Model\ResourceModel\Selection;


class Collection
{
    /**
     * @var \Magento\CatalogInventory\Api\StockConfigurationInterface
     */
    private $stockConfiguration;

    /**
     * Collection constructor.
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     */
    public function __construct(
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
    )
    {
        $this->stockConfiguration = $stockConfiguration;
    }

    /**
     *
     * @param \Magento\CatalogInventory\Model\Configuration $stockConfiguration
     * @param int $scopeId
     * @return int
     */
    public function afterAddQuantityFilter(\Magento\Bundle\Model\ResourceModel\Selection\Collection $subject, $collection)
    {
        $scopeId = $this->stockConfiguration->getDefaultScopeId();
        $collection->getSelect()->where('stock.website_id = ?', $scopeId);
        return $collection;
    }
}