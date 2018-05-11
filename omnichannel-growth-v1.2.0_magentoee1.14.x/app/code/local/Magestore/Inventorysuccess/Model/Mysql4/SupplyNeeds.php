<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Adjuststock Resource Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Mysql4_SupplyNeeds extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct() {
        $this->_init('inventorysuccess/supplyNeeds', 'entity_id');
    }

    /**
     * get product for supply neeeds
     * @param $topFilter
     * @param $sort
     * @param $dir
     * @return Magestore_Inventorysuccess_Model_Mysql4_SupplyNeeds_Product_Collection
     */
    public function getProductSupplyNeedsCollection($topFilter, $sort, $dir)
    {
        $topFilter = unserialize(base64_decode($topFilter));
//        $this->_filterDates($topFilter, array('from_date', 'to_date'));
        $salesPeriod = $topFilter['sales_period'];
        $forecastDateTo = $topFilter['forecast_date_to'];
        $supplyNeedsService = Magestore_Coresuccess_Model_Service::supplyNeedsService();
        if ($salesPeriod == Magestore_Inventorysuccess_Model_SupplyNeeds::CUSTOM_RANGE) {
            $fromDate = date('Y-m-d', strtotime($topFilter['from_date']));
            $toDate = date('Y-m-d', strtotime($topFilter['to_date']));
        } else {
            $fromDate = $supplyNeedsService->getFromDateBySalePeriod($salesPeriod);
            $toDate = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
        }
        $fromDate .= ' 00:00:00';
        $toDate .= ' 23:59:59';
        $numberOfDaySalesPeriod = ceil((strtotime($toDate) - strtotime($fromDate)) / (60 * 60 * 24));
        $numberForecastDay = floor((strtotime($forecastDateTo) - strtotime(date('Y-m-d'))) / (60 * 60 * 24));
        $topFilter['from_date'] = $fromDate;
        $topFilter['to_date'] = $toDate;
        $topFilter['number_of_day_sales_period'] = $numberOfDaySalesPeriod;
        $topFilter['number_forecast_day'] = $numberForecastDay;
        $this->_removeTermTable($this->_getTempTables());
        // create temp_warehouse_product table
        $this->_createTermWarehouseProduct($topFilter);
        // create temp_shipment_item table
        $this->_createTermWarehouseShipmentItems($topFilter);
        /** @var Magestore_Inventorysuccess_Model_Mysql4_SupplyNeeds_Product_Collection $collection */
        $collection = Mage::getResourceModel('inventorysuccess/supplyNeeds_product_collection');

        //$collection->addAttributeToSelect('name');
        $alias = 'name_table';
        $attribute = Mage::getSingleton('eav/config')
            ->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'name');
        $collection->getSelect()->joinLeft(
            array($alias => $attribute->getBackendTable()),
            "e.entity_id = $alias.entity_id AND $alias.attribute_id={$attribute->getId()} AND $alias.store_id=0",
            array('name' => 'value')
        );

        $collection->getSelect()->join(
            array(
                'warehouse_product' => Mage::getSingleton('core/resource')->getTableName(Magestore_Inventorysuccess_Model_SupplyNeeds::TEMP_WAREHOUSE_PRODUCTS)
            ),
            'e.entity_id = warehouse_product.entity_id',
            array(
                'current_qty' => 'warehouse_product.current_qty'
            )
        );
        $collection->getSelect()->join(
            array(
                'warehouse_shipment_item' => Mage::getSingleton('core/resource')->getTableName(Magestore_Inventorysuccess_Model_SupplyNeeds::TEMP_WAREHOUSE_SHIPMENT_ITEM)
            ),
            "e.entity_id = warehouse_shipment_item.product_id",
            array(
                'total_sold' => 'warehouse_shipment_item.total_sold'
            )
        )
            ->group('warehouse_shipment_item.product_id')
            ->where("GREATEST((warehouse_shipment_item.total_sold / {$numberOfDaySalesPeriod} * {$numberForecastDay} - warehouse_product.current_qty),0) > ?", 0)
        ;
        $collection->getSelect()->columns(
            array(
            'avg_qty_ordered' => new Zend_Db_Expr("(warehouse_shipment_item.total_sold / {$numberOfDaySalesPeriod})"),
            'total_sold' => "warehouse_shipment_item.total_sold",
            'current_qty' => 'warehouse_product.current_qty',
            'availability_date' => new Zend_Db_Expr("DATE_ADD(CURDATE(),INTERVAL(FLOOR(warehouse_product.current_qty / (warehouse_shipment_item.total_sold / {$numberOfDaySalesPeriod}))) DAY)"),
            'supply_needs' => new Zend_Db_Expr("CEIL(GREATEST((CEIL(warehouse_shipment_item.total_sold / {$numberOfDaySalesPeriod} * {$numberForecastDay}) - warehouse_product.current_qty),0))"),
            )
        );

        /* prepare for filter "avg_qty_ordered" and "availability_date" and "supply_needs" */
        $filterValue = array(
            'avg_qty_ordered' => 'warehouse_shipment_item.total_sold / '.$numberOfDaySalesPeriod,
            'availability_date' => 'DATE_ADD(CURDATE(),INTERVAL(FLOOR(warehouse_product.current_qty / (warehouse_shipment_item.total_sold / '.$numberOfDaySalesPeriod.'))) DAY)',
            'supply_needs' => 'CEIL(GREATEST((CEIL(warehouse_shipment_item.total_sold / '.$numberOfDaySalesPeriod.' * '.$numberForecastDay.') - warehouse_product.current_qty),0))'
        );
        Mage::unregister('filter_supplyneeds_forecasting');
        Mage::register('filter_supplyneeds_forecasting',$filterValue);

        $collection->setIsGroupCountSql(true);
        $collection->getSelectCountSql();
        $collection = $this->getSortData($collection, $sort, $dir, $topFilter);
        return $collection;

    }

    /**
     * remove term table
     * @param $tempTables
     */
    protected function _removeTermTable($tempTables) {
        foreach ($tempTables as $tempTable) {
            $sql = "DROP TABLE IF EXISTS " . Mage::getSingleton('core/resource')->getTableName($tempTable) . ";";
            $this->_getWriteAdapter()->query($sql);
        }
    }

    /**
     * get all term tables
     * @return array
     */
    protected function _getTempTables()
    {
        return array(
            Magestore_Inventorysuccess_Model_SupplyNeeds::TEMP_WAREHOUSE_PRODUCTS,
            Magestore_Inventorysuccess_Model_SupplyNeeds::TEMP_SHIPMENT_ITEM,
            Magestore_Inventorysuccess_Model_SupplyNeeds::TEMP_WAREHOUSE_SHIPMENT_ITEM
        );
    }

    /**
     * create term table
     * @param $tempTable
     * @param $collection
     */
    protected function _createTempTable($tempTable, $collection) {
        $sql = "CREATE TEMPORARY TABLE " . Mage::getSingleton('core/resource')->getTableName($tempTable) . " ";
        $sql .= $collection->getSelect()->__toString();
        $this->_getWriteAdapter()->query($sql);
    }

    /**
     * Create TEMP_WAREHOUSE_PRODUCTS
     * @param $topFilter
     */
    protected function _createTermWarehouseProduct($topFilter)
    {
        $warehouseIds = isset($topFilter['warehouse_ids']) ? $topFilter['warehouse_ids'] : 0 ;
        /** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection */
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->addAttributeToSelect(array('name', 'sku'));
        $collection->getSelect()->join(
            array('cataloginventory' => Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_item')),
            'e.entity_id = cataloginventory.product_id',
            array('stock_id')
        )->where('cataloginventory.stock_id in (?)', $warehouseIds);;
//        $collection->addFieldToFilter('cataloginventory.stock_id', array('in' => $warehouseIds));
        $collection->getSelect()->columns(
            array(
                'current_qty' => new Zend_Db_Expr("SUM(cataloginventory.total_qty)")
            )
        );
        $collection->getSelect()->group('entity_id');
        $this->_createTempTable(Magestore_Inventorysuccess_Model_SupplyNeeds::TEMP_WAREHOUSE_PRODUCTS, $collection);
    }

    /**
     * Create TEMP_WAREHOUSE_SHIPMENT_ITEM
     * @param $topFilter
     */
    protected function _createTermWarehouseShipmentItems($topFilter)
    {
        $fromDate = $topFilter['from_date'];
        $toDate = $topFilter['to_date'];
        $warehouseIds = isset($topFilter['warehouse_ids']) ? $topFilter['warehouse_ids'] : 0 ;
        /** @var Mage_Sales_Model_Mysql4_Order_Shipment_Collection $shipmentCollection */

        $order_item_table = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
        $foreignField = "(warehouse_order_item.product_type)";
        $productId_in_simple = "main_table.product_id";
        $productId_in_Configuration = " (select (product_id) from {$order_item_table} where parent_item_id = warehouse_order_item.item_id) ";
        $configuration_code = Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;
        $product_ids = '(IF( ('.$foreignField.' = "'.$configuration_code.'") , '.$productId_in_Configuration.' ,'.$productId_in_simple.' ))';

        $collection = Mage::getResourceModel('sales/order_shipment_item_collection');
        $collection->addFieldToSelect('entity_id')
             ->addFieldToFilter('warehouse_order_item.created_at', array('gteq' => $fromDate))
             ->addFieldToFilter('warehouse_order_item.created_at', array('lteq' => $toDate))
             ->addFieldToFilter('main_table.warehouse_id', array('in' => $warehouseIds));
        $collection->getSelect()->join(
            array('warehouse_order_item' => $order_item_table),
            "main_table.product_id = warehouse_order_item.product_id and main_table.order_item_id = warehouse_order_item.item_id",
            array(
                'total_sold' => new Zend_Db_Expr("SUM(main_table.qty)"),
                'product_id' => new Zend_Db_Expr("{$product_ids}"),
            )
        );
        $collection->getSelect()->group("{$product_ids}");
        $this->_createTempTable(Magestore_Inventorysuccess_Model_SupplyNeeds::TEMP_WAREHOUSE_SHIPMENT_ITEM, $collection);
    }

    /**
     * sort data
     * @param $collection
     * @param $sort
     * @param $dir
     * @param $topFilter
     * @return mixed
     */
    public function getSortData($collection, $sort, $dir, $topFilter)
    {
        $salesPeriod = isset($topFilter['sales_period'])?$topFilter['sales_period']:0;
        $forecastDateTo = isset($topFilter['forecast_date_to'])?$topFilter['forecast_date_to']:0;
        if ($salesPeriod == Magestore_Inventorysuccess_Model_SupplyNeeds::CUSTOM_RANGE) {
            $fromDate = date('Y-m-d', strtotime($topFilter['from_date']));
            $toDate = date('Y-m-d', strtotime($topFilter['to_date']));
        } else {
            $supplyNeedsService = Magestore_Coresuccess_Model_Service::supplyNeedsService();
            $fromDate = $supplyNeedsService->getFromDateBySalePeriod($salesPeriod);
            $toDate = date('Y-m-d');
        }
        $fromDate .= ' 00:00:00';
        $toDate .= ' 23:59:00';
        $numberOfDaySalesPeriod = floor((strtotime($toDate) - strtotime($fromDate))/(60*60*24));
        $numberForecastDay = floor((strtotime($forecastDateTo) - strtotime($toDate))/(60*60*24));

        switch ($sort) {
            case 'avg_qty_ordered':
                $collection->getSelect()->order("(warehouse_shipment_item.total_sold / {$numberOfDaySalesPeriod}) " . $dir);
                break;
            case 'total_sold':
                $collection->getSelect()->order("warehouse_shipment_item.total_sold ". $dir);
                break;
            case 'current_qty':
                $collection->getSelect()->order("warehouse_product.current_qty ". $dir);
                break;
            case 'availability_date':
                $collection->getSelect()->order("DATE_ADD(CURDATE(),INTERVAL(warehouse_product.current_qty / (warehouse_shipment_item.total_sold / {$numberOfDaySalesPeriod})) DAY) ". $dir);
                break;
            case 'supply_needs':
                $collection->getSelect()->order("(GREATEST((warehouse_shipment_item.total_sold / {$numberOfDaySalesPeriod} * {$numberForecastDay} - warehouse_product.current_qty),0)) ". $dir);
                break;
        }
        return $collection;
    }

    /**
     * Convert dates in array from localized to internal format
     *
     * @param   array $array
     * @param   array $dateFields
     * @return  array
     */
    protected function _filterDates($array, $dateFields)
    {
        if (empty($dateFields)) {
            return $array;
        }
        $filterInput = new Zend_Filter_LocalizedToNormalized(array(
            'date_format' => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
        ));
        $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
            'date_format' => Varien_Date::DATE_INTERNAL_FORMAT
        ));

        foreach ($dateFields as $dateField) {
            if (array_key_exists($dateField, $array) && !empty($dateField)) {
                $array[$dateField] = $filterInput->filter($array[$dateField]);
                $array[$dateField] = $filterInternal->filter($array[$dateField]);
            }
        }
        return $array;
    }
}