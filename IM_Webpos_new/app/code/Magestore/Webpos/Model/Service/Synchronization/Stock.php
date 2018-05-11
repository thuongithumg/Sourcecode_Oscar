<?php

/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

namespace Magestore\Webpos\Model\Service\Synchronization;

/**
 * class \Magestore\Webpos\Model\Service\Synchronization\Stock
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @module      Webpos
 * @author      Magestore Developer
 */
class Stock extends \Magestore\Webpos\Model\Service\Synchronization implements \Magestore\Webpos\Api\Synchronization\StockInterface
{
    const SYNCHRONIZATION_TYPE = 'stock';

    const SYNCHRONIZATION_CONFIG_LINK = 'ms_webpos/sync_time/stock';

    const SYNCHRONIZATION_CONFIG_UPDATE = 'ms_webpos/process_update/stock';

    const SYNCHRONIZATION_CONFIG_USE = 'webpos/offline/stock_sync_index';

    const SYNCHRONIZATION_TABLE = 'ms_webpos_cataloginventory_stock_flat';

    public function prepareSynchronizationData($updatedTime, $storeId = null, $curentPage = 1)
    {
        $attributeName = $this->entityAttribute->loadByCode(4, 'name');

        $updatedCondition = $this->getUpdatedCondition($updatedTime);

        $attributeKey = 'entity_id';
        if ($this->isMagentoEnterprise()) {
            $attributeKey = 'row_id';
        }

        $sql = 'SELECT stock.item_id,stock.stock_id,stock.product_id,'
            . 'stock.qty,stock.is_in_stock,stock.manage_stock,'
            . 'stock.use_config_manage_stock,stock.backorders,stock.use_config_backorders,'
            . 'stock.min_sale_qty,stock.use_config_min_sale_qty,stock.max_sale_qty,'
            . 'stock.use_config_max_sale_qty,stock.is_qty_decimal,stock.updated_time,'
            . 'stock.qty_increments,stock.website_id,e.sku,at_name.value as name'
            . ' FROM ' . $this->resource->getTableName('cataloginventory_stock_item') . ' AS stock'
            . ' JOIN ' . $this->resource->getTableName('catalog_product_entity') . ' AS e'
            . ' ON stock.product_id = e.' . $attributeKey
            . ' JOIN ' . $this->resource->getTableName('catalog_product_entity_varchar') . ' AS at_name'
            . ' ON at_name.' . $attributeKey . ' = e.' . $attributeKey
            . ' WHERE at_name.store_id = 0 AND attribute_id = ' . $attributeName->getId() . $updatedCondition;

        $sql .= ' LIMIT ' . static::PAGESIZE . " OFFSET " . (int)static::PAGESIZE * ($curentPage - 1);

        return $this->connection->query($sql);
    }

    public function getTotalUpdatedData($updatedTime, $storeId = null)
    {
        $attributeName = $this->entityAttribute->loadByCode(4, 'name');

        $updatedCondition = $this->getUpdatedCondition($updatedTime);

        $attributeKey = 'entity_id';
        if ($this->isMagentoEnterprise()) {
            $attributeKey = 'row_id';
        }

        $sql = 'SELECT COUNT(*)'
            . ' FROM ' . $this->resource->getTableName('cataloginventory_stock_item') . ' AS stock'
            . ' JOIN ' . $this->resource->getTableName('catalog_product_entity') . ' AS e'
            . ' ON stock.product_id = e.' . $attributeKey
            . ' JOIN ' . $this->resource->getTableName('catalog_product_entity_varchar') . ' AS at_name'
            . ' ON at_name.' . $attributeKey . ' = e.' . $attributeKey
            . ' WHERE at_name.store_id = 0 AND attribute_id = ' . $attributeName->getId() . $updatedCondition;

        return $this->connection->fetchCol($sql)[0];
    }

    public function getUpdatedCondition($updatedTime = null)
    {
        $updatedCondition = '';
        if ($updatedTime) {
            $updatedCondition = ' AND stock.updated_time >= "' . $updatedTime . '"';
        }
        return $updatedCondition;
    }
}