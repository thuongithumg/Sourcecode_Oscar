<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Import entity product model
 *
 * @category    Mage
 * @package     Mage_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magestore_Inventorysuccess_Model_ImportExport_Rewrite_Import_Entity_Product extends Mage_ImportExport_Model_Import_Entity_Product
{

    protected $warehouseIds = array();
    protected $warehouses = array();
    protected $warehouseAdjustStock = array();
    protected $warehouseAdjustIds = array();
    protected $warehouseProductLocations = array();
    protected $warehouseLocationIds = array();
    protected $nonWarehouse;
    /**
     * Stock item saving.
     *
     * @return Mage_ImportExport_Model_Import_Entity_Product
     */
    protected function _saveStockItem()
    {
        $defaultStockData = array(
            'manage_stock'                  => 1,
            'use_config_manage_stock'       => 1,
            'qty'                           => 0,
            'min_qty'                       => 0,
            'use_config_min_qty'            => 1,
            'min_sale_qty'                  => 1,
            'use_config_min_sale_qty'       => 1,
            'max_sale_qty'                  => 10000,
            'use_config_max_sale_qty'       => 1,
            'is_qty_decimal'                => 0,
            'backorders'                    => 0,
            'use_config_backorders'         => 1,
            'notify_stock_qty'              => 1,
            'use_config_notify_stock_qty'   => 1,
            'enable_qty_increments'         => 0,
            'use_config_enable_qty_inc'     => 1,
            'qty_increments'                => 0,
            'use_config_qty_increments'     => 1,
            'is_in_stock'                   => 0,
            'low_stock_date'                => null,
            'stock_status_changed_auto'     => 0,
            'is_decimal_divided'            => 0
        );

        $entityTable = $this->getResourceModel('cataloginventory/stock_item')->getMainTable();
        $helper      = $this->getHelper('catalogInventory');

        /** @var import with warehouse */

        if (!count($this->warehouseIds)) {
            /** @var Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Collection $warehouseCollection */
            $warehouseCollection = Mage::getResourceModel('inventorysuccess/warehouse_collection');
            /** @var Magestore_Inventorysuccess_Model_Warehouse $warehouse */
            foreach ($warehouseCollection as $warehouse) {
                $this->warehouseIds[] = $warehouse->getId();
                $this->warehouses[$warehouse->getId()] = $warehouse->getData();
            }
        }
        /** @var end import with warehouse */

        while ($bunch = $this->getNextBunch()) {
            $stockData = array();

            // Format bunch to stock data rows
            foreach ($bunch as $rowNum => $rowData) {
                $this->_filterRowData($rowData);
                if (!$this->isRowAllowedToImport($rowData, $rowNum)) {
                    continue;
                }
                // only SCOPE_DEFAULT can contain stock data
                if (self::SCOPE_DEFAULT != $this->getRowScope($rowData)) {
                    continue;
                }

                $row = array();
                $row['product_id'] = $this->_newSku[$rowData[self::COL_SKU]]['entity_id'];
                $row['stock_id'] = 1;

                /** @var $stockItem Mage_CatalogInventory_Model_Stock_Item */
                $stockItem = $this->getModel('cataloginventory/stock_item');
                $stockItem->loadByProduct($row['product_id']);
                $existStockData = $stockItem->getData();

                $row = array_merge(
                    $defaultStockData,
                    array_intersect_key($existStockData, $defaultStockData),
                    array_intersect_key($rowData, $defaultStockData),
                    $row
                );

                $stockItem->setData($row);
                unset($row);
                if ($helper->isQty($this->_newSku[$rowData[self::COL_SKU]]['type_id'])) {

                    /** get qty product when have warehouse */
                    $this->nonWarehouse = true;
                    if (!empty($this->warehouseIds)) {
                        $qty = $this->getQtyProductToImport($stockItem, $rowData);
                        $warehouseService = Magestore_Coresuccess_Model_Service::warehouseService();
                        /** if product is not in any warehouse, the product will be added to primary warehouse with qty of catalog product*/
                        if (!$this->nonWarehouse) {
                            $stockItem->setQty($qty);
                        }
                        if ($this->nonWarehouse) {
                            $primaryWarehouse = $warehouseService->getPrimaryWarehouse();
                            $this->warehouseAdjustStock[$primaryWarehouse->getId()]['products'][$stockItem->getProductId()] = array(
                                'adjust_qty' => $stockItem->getQty(),
                                'product_name' => isset($rowData['name'])?$rowData['name']:null,
                                'product_sku' => isset($rowData['sku'])?$rowData['sku']:null,
                                'old_qty' => 0
                            );
                            if (!in_array($primaryWarehouse->getId(), $this->warehouseAdjustIds)) {
                                $this->warehouseAdjustIds[] = $primaryWarehouse->getId();
                            }
                        }
                    }
                    /** end get qty product when have warehouse */

                    if ($stockItem->verifyNotification()) {
                        $stockItem->setLowStockDate(Mage::app()->getLocale()
                            ->date(null, null, null, false)
                            ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT)
                        );
                    }
                    $stockItem->setStockStatusChangedAutomatically((int) !$stockItem->verifyStock());
                } else {
                    $stockItem->setQty(0);
                }
                $stockData[] = $stockItem->unsetOldData()->getData();
            }

            // Insert rows
            if ($stockData) {
                $this->_connection->insertOnDuplicate($entityTable, $stockData);
            }

            /** create adjust stock */
            $this->createAdjustStock();

            /** update location for product in warehouse */
            $this->updateLocationProductWarehouse();

        }
        return $this;
    }

    /**
     * get qty product by warehouse to import product to catalog
     * @param $stockItem
     * @return int
     */
    public function getQtyProductToImport($stockItem, $rowData)
    {
        $qty = 0;
        $warehouseStockService = Magestore_Coresuccess_Model_Service::warehouseStockService();
        foreach ($this->warehouseIds as $warehouseId) {
            /** @var Magestore_Inventorysuccess_Model_Warehouse_Product $warehouseStock */
            $warehouseStock = $warehouseStockService->getStocks($warehouseId, array($stockItem->getProductId()))
                ->setPageSize(1)
                ->setCurPage(1)
                ->getFirstItem();
            $oldWarehouseProductQty = 0;
            /** check qty product in warehouse */
            if (isset($rowData['qty_'.$warehouseId])) {
                $this->nonWarehouse = false;
                $qty += $rowData['qty_'.$warehouseId];
                if ($warehouseStock->getId()) {
                    $qty -= $warehouseStock->getQtyToShip();
                    $oldWarehouseProductQty = $warehouseStock->getTotalQty();
                }
                $newWarehouseProductQty = $rowData['qty_'.$warehouseId];
                if ($oldWarehouseProductQty != $newWarehouseProductQty) {
                    $this->warehouseAdjustStock[$warehouseId]['products'][$stockItem->getProductId()] = array(
                        'adjust_qty' => $newWarehouseProductQty,
                        'product_name' => isset($rowData['name'])?$rowData['name']:null,
                        'product_sku' => isset($rowData['sku'])?$rowData['sku']:null,
                        'old_qty' => 0
                    );
                    if (!in_array($warehouseId, $this->warehouseAdjustIds)) {
                        $this->warehouseAdjustIds[] = $warehouseId;
                    }
                }
            } else {
                if ($warehouseStock->getId()) {
                    $this->nonWarehouse = false;
                    $qty += $warehouseStock->getTotalQty() - $warehouseStock->getQtyToShip();
                }
            }
            /** check shelf location product in warehouse */
            if (isset($rowData['location_'.$warehouseId])) {
                $this->warehouseProductLocations[$warehouseId][$stockItem->getProductId()] = $rowData['location_'.$warehouseId];
                if (!in_array($warehouseId, $this->warehouseLocationIds)) {
                    $this->warehouseLocationIds[] = $warehouseId;
                }
            }
        }
        return $qty;
    }

    /**
     * create adjust stock
     */
    public function createAdjustStock()
    {
        if (!empty($this->warehouseAdjustStock)) {
            foreach ($this->warehouseAdjustIds as $warehouseId) {
                $productToAdjusts = $this->warehouseAdjustStock[$warehouseId];
                $adjustData['products'] = $productToAdjusts['products'];

                $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_ID] = $warehouseId;
                $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_CODE] = isset($this->warehouses[$warehouseId]['warehouse_code']) ?
                    $this->warehouses[$warehouseId]['warehouse_code'] :
                    null;
                $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::WAREHOUSE_NAME] = isset($this->warehouses[$warehouseId]['warehouse_name']) ?
                    $this->warehouses[$warehouseId]['warehouse_name'] :
                    null;
                $adjustData[Magestore_Inventorysuccess_Model_Adjuststock::REASON] = Mage::helper('inventorysuccess')->__('Import Products');
                if (!empty($productToAdjusts)) {
                    /** @var Magestore_Inventorysuccess_Model_Adjuststock $adjustStock */
                    $adjustStock = Mage::getModel('inventorysuccess/adjuststock');

                    $adjustStockService = Magestore_Coresuccess_Model_Service::adjustStockService();
                    /* create stock adjustment, require products */
                    $adjustStockService->createAdjustment($adjustStock, $adjustData);

                    /* created adjuststock or not */
                    if($adjustStock->getId()) {
                        /* complete stock adjustment */
                        $adjustStockService->complete($adjustStock, false);
                    }
                }
            }
        }
    }

    /**
     * update product shelf location to warehouse
     */
    public function updateLocationProductWarehouse()
    {
        if (!empty($this->warehouseProductLocations)) {
            foreach ($this->warehouseLocationIds as $warehouseId) {
                if (!empty($this->warehouseProductLocations[$warehouseId])) {
                    $locations = $this->warehouseProductLocations[$warehouseId];
                    $stockRegistryService = Magestore_Coresuccess_Model_Service::stockRegistryService();
                    $stockRegistryService->updateLocation($warehouseId, $locations);
                }
            }
        }
    }
}
