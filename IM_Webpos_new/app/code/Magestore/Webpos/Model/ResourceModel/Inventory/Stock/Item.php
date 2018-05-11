<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\ResourceModel\Inventory\Stock;

use \Magento\CatalogInventory\Model\Stock;
use Magento\CatalogInventory\Api\StockConfigurationInterface;

/**
 * Stock item resource model
 */
class Item extends \Magento\CatalogInventory\Model\ResourceModel\Stock\Item
{
    
    /**
     * @var StockConfigurationInterface
     */
    private $stockConfiguration;    

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     */
    public function addStockDataToCollection($collection)
    {
        $websiteId = $this->getStockConfiguration()->getDefaultScopeId();
        $joinCondition = $this->getConnection()->quoteInto(
            'stock_item_index.website_id = ? AND',
            $websiteId
        );

        $joinCondition = $this->getConnection()->quoteInto(
            'e.entity_id = stock_item_index.product_id',
            $websiteId
        );

//        $joinCondition .= $this->getConnection()->quoteInto(
//            ' AND stock_item_index.stock_id = ?',
//            Stock::DEFAULT_STOCK_ID
//        );

        $collection->getSelect()->join(
            ['stock_item_index' => $this->getMainTable()],
            $joinCondition,
            ['item_id' => 'item_id',
                'stock_id' => 'stock_id',
                'product_id' => 'product_id',
                'qty' => 'qty',
                'is_in_stock' => 'is_in_stock',
                'manage_stock' => 'manage_stock',
                'use_config_manage_stock' => 'use_config_manage_stock',
                'backorders' => 'backorders',
                'use_config_backorders' => 'use_config_backorders',
                'min_sale_qty' => 'min_sale_qty',
                'use_config_min_sale_qty' => 'use_config_min_sale_qty',
                'max_sale_qty' => 'max_sale_qty',
                'use_config_max_sale_qty' => 'use_config_max_sale_qty',
                'is_qty_decimal' => 'is_qty_decimal',
                'updated_time' => 'updated_time',
            ]
        );
        $collection->getSelect()->join(
            ['ea' => $this->getTable('eav_attribute')],
            "ea.entity_type_id = 4 AND ea.attribute_code = 'name'",
            [
                'name_attribute_id' => 'attribute_id'
            ]
        );

        if (!$this->isMagentoEnterprise()) {
            $collection->getSelect()->join(
                ['cpev' => $this->getTable('catalog_product_entity_varchar')],
                "cpev.entity_id = e.entity_id AND cpev.attribute_id = ea.attribute_id",
                [
                    'name' => 'value'
                ]
            );
        } else {
            $collection->getSelect()->join(
                ['cpev' => $this->getTable('catalog_product_entity_varchar')],
                "cpev.row_id = e.row_id AND cpev.attribute_id = ea.attribute_id",
                [
                    'name' => 'value'
                ]
            );
        }

        return $collection;
    }
    
    /**
     * @return StockConfigurationInterface
     *
     * @deprecated
     */
    private function getStockConfiguration()
    {
        if ($this->stockConfiguration === null) {
            $this->stockConfiguration = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\CatalogInventory\Api\StockConfigurationInterface');
        }
        return $this->stockConfiguration;
    }

    /**
     * @return bool
     */
    public function isMagentoEnterprise()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productMetadata = $objectManager->get('Magento\Framework\App\ProductMetadataInterface');
        $edition = $productMetadata->getEdition();
        if ($edition == 'Enterprise') {
            return true;
        } else {
            return false;
        }
    }
    
}