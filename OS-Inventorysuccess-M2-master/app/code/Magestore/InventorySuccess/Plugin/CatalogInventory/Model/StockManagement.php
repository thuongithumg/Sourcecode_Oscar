<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\InventorySuccess\Plugin\CatalogInventory\Model;

use Magestore\InventorySuccess\Api\Warehouse\WarehouseManagementInterface;

class StockManagement
{
    
    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $registry; 
    
    public function __construct(\Magento\Framework\Registry $registry)
    {
        $this->registry = $registry;
    }
    
    public function beforeRegisterProductsSale(\Magento\CatalogInventory\Model\StockManagement $stockManagement, $items, $websiteId = null)
    {
        $this->registry->register(WarehouseManagementInterface::BEFORE_SUBTRACT_SALES_QTY, true);
        return [$items, $websiteId];
    }    
    
    public function afterRegisterProductsSale(\Magento\CatalogInventory\Model\StockManagement $stockManagement, $fullSaveItems)
    {
        $this->registry->unregister(WarehouseManagementInterface::BEFORE_SUBTRACT_SALES_QTY);
        return $fullSaveItems;
    }
}