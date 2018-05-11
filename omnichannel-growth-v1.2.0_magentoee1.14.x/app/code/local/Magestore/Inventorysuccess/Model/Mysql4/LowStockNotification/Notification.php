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
class Magestore_Inventorysuccess_Model_Mysql4_LowStockNotification_Notification extends Mage_Core_Model_Mysql4_Abstract
{

    protected $batchCount = 1000;

    public function _construct()
    {
        $this->_init('inventorysuccess/lowStockNotification_notification', 'notification_id');
    }

    /**
     * @param $rule
     * @param $productIds
     * @return null
     */
    public function getProductNotificationBySystem($rule, $productIds)
    {
        $lowstockThreshold = $rule['lowstock_threshold'];
        $lowstockThresholdType = $rule['lowstock_threshold_type'];
        $lowstockThresholdQty = $rule['lowstock_threshold_qty'];
        $salesPeriod = $rule['sales_period'];
        $toDate = date('Y-m-d');
        $fromDate = $this->getFromDateBySalePeriod($salesPeriod);
        $fromDate .= ' 00:00:00';
        $toDate .= ' 23:59:59';
        /** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection */
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->addAttributeToSelect('name');
        $collection->addFieldToFilter('entity_id', array('in' => $productIds));
        $collection->addFieldToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE);
        $collection->getSelect()->join(
            array('cataloginventory' => Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_item')),
            'e.entity_id = cataloginventory.product_id AND stock_id = 1',
            array(
                'current_qty' => 'cataloginventory.qty',
            )
        );
        if ($lowstockThresholdType == Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_LOWSTOCK_THRESHOLD_PRODUCT_QTY) {
            $collection->getSelect()->where("cataloginventory.qty <= ?", $lowstockThresholdQty)
                ->group('e.entity_id');
        }
        if ($lowstockThresholdType == Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_LOWSTOCK_THRESHOLD_SALE_DAY) {
            $collection->getSelect()->join(
                array('order_item' => Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item')),
                'e.entity_id = order_item.product_id',
                array(
                    'total_sold' => new Zend_Db_Expr("SUM(order_item.qty_ordered)"),
                    'sold_per_day' => new Zend_Db_Expr("SUM(order_item.qty_ordered) / {$salesPeriod}"),
                    'availability_date' => new Zend_Db_Expr("DATE_ADD(CURDATE(),INTERVAL(FLOOR(cataloginventory.qty / (SUM(order_item.qty_ordered) / {$salesPeriod}))) DAY)"),
                    'availability_days' => new Zend_Db_Expr("FLOOR(GREATEST(cataloginventory.qty / (SUM(order_item.qty_ordered) / {$salesPeriod}),0))")
                )
            )
                ->where("order_item.created_at >= ?", $fromDate)
                ->where("order_item.created_at <= ?", $toDate)
                ->group('order_item.product_id')
                ->having("GREATEST((SUM(order_item.qty_ordered) / {$salesPeriod} * {$lowstockThreshold} - cataloginventory.qty),0) > ?", 0);
        }
        if (count($collection)) {
            $type = Magestore_Inventorysuccess_Model_LowStockNotification_Notification::NOTIFY_TYPE_SYSTEM;
            if ($lowStockNotificationId = $this->addProductToNotification($rule, $collection, $type, null)) {
                try {
                    /** add notification to inbox */
                    $this->addToInbox($rule, $lowStockNotificationId, null);
                    /** @var  Magestore_Inventorysuccess_Model_LowStockNotification_Rule $ruleModel */
                    $ruleModel = Mage::getModel('inventorysuccess/lowStockNotification_rule');
                    $ruleModel->load($rule['rule_id']);
                    $ruleModel->setUpdatedAt(date('Y-m-d H:i:s'));
                    $ruleModel->save();
                } catch (\Exception $e) {
                    throw $e;
                }
                return $lowStockNotificationId;
            }
        }
    }

    /**
     * @param $rule
     * @param $productIds
     * @param $warehouseIds
     * @return array|null
     */
    public function getProductNotificationByWarehouse($rule, $productIds, $warehouseIds)
    {
        $lowstockThreshold = $rule['lowstock_threshold'];
        $lowstockThresholdType = $rule['lowstock_threshold_type'];
        $lowstockThresholdQty = $rule['lowstock_threshold_qty'];
        $salesPeriod = $rule['sales_period'];
        $toDate = date('Y-m-d');
        $fromDate = $this->getFromDateBySalePeriod($salesPeriod);
        $fromDate .= ' 00:00:00';
        $toDate .= ' 23:59:59';
        $warehouseIds = explode(',', $warehouseIds);
        $result = array();
        foreach ($warehouseIds as $warehouseId) {
            /** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection */
            $collection = Mage::getResourceModel('catalog/product_collection');
            $collection->addAttributeToSelect('name');
            $collection->addFieldToFilter('entity_id', array('in' => $productIds));
            $collection->addFieldToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE);
            $collection->getSelect()->join(
                array('cataloginventory' => Mage::getSingleton('core/resource')->getTableName('cataloginventory_stock_item')),
                "e.entity_id = cataloginventory.product_id",
                array(
                    'current_qty' => 'cataloginventory.qty',
                    'cataloginventory.total_qty'
                )
            )->where('cataloginventory.stock_id = ?', $warehouseId);

            if ($lowstockThresholdType == Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_LOWSTOCK_THRESHOLD_PRODUCT_QTY) {
                $collection->getSelect()->where("cataloginventory.qty <= ?", $lowstockThresholdQty)
                    ->group('e.entity_id');
            }
            if ($lowstockThresholdType == Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_LOWSTOCK_THRESHOLD_SALE_DAY) {

                /* Start : create temptable data */
                $order_item_table = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
                $foreignField = "(warehouse_order_item.product_type)";
                $productId_in_simple = "main_table.product_id";
                $productId_in_Configuration = " (select (product_id) from {$order_item_table} where parent_item_id = warehouse_order_item.item_id) ";
                $configuration_code = Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;
                $product_ids = '(IF( ('.$foreignField.' = "'.$configuration_code.'") , '.$productId_in_Configuration.' ,'.$productId_in_simple.' ))';

                $collectionTemp = Mage::getResourceModel('sales/order_shipment_item_collection');
                $collectionTemp->addFieldToSelect('entity_id')
                    ->addFieldToSelect('warehouse_id')
                    ->addFieldToFilter('warehouse_order_item.created_at', array('gteq' => $fromDate))
                    ->addFieldToFilter('warehouse_order_item.created_at', array('lteq' => $toDate))
                    ->addFieldToFilter('main_table.warehouse_id', array('in' => $warehouseId));
                $collectionTemp->getSelect()->join(
                    array('warehouse_order_item' => $order_item_table),
                    "main_table.product_id = warehouse_order_item.product_id and main_table.order_item_id = warehouse_order_item.item_id",
                    array(
                        'qty_shipped' => new Zend_Db_Expr("SUM(main_table.qty)"),
                        'product_id' => new Zend_Db_Expr("{$product_ids}"),
                    )
                );
                $collectionTemp->getSelect()->group("{$product_ids}");
                $this->_removeTermTable('os_temtable_data_warehouse_shipment_item');
                $this->_createTempTable('os_temtable_data_warehouse_shipment_item', $collectionTemp);
                /* End : create temptable data */

                $collection->getSelect()->join(
                    array('warehouse_shipment_item' => Mage::getSingleton('core/resource')->getTableName('os_temtable_data_warehouse_shipment_item')),
                    "e.entity_id = warehouse_shipment_item.product_id and warehouse_shipment_item.warehouse_id = {$warehouseId}",
                    array(
                        'total_sold' => new Zend_Db_Expr("SUM(warehouse_shipment_item.qty_shipped)"),
                        'sold_per_day' => new Zend_Db_Expr("SUM(warehouse_shipment_item.qty_shipped) / {$salesPeriod}"),
                        'availability_date' => new Zend_Db_Expr("DATE_ADD(CURDATE(),INTERVAL(FLOOR(cataloginventory.total_qty / (SUM(warehouse_shipment_item.qty_shipped) / {$salesPeriod}))) DAY)"),
                        'availability_days' => new Zend_Db_Expr("FLOOR(GREATEST(cataloginventory.total_qty / (SUM(warehouse_shipment_item.qty_shipped) / {$salesPeriod}),0))")
                    )
                )
                    ->group('warehouse_shipment_item.product_id')
                    ->having("GREATEST((SUM(warehouse_shipment_item.qty_shipped) / {$salesPeriod} * {$lowstockThreshold} - cataloginventory.total_qty),0) > ?", 0);
            }

            if (count($collection)) {
                $type = Magestore_Inventorysuccess_Model_LowStockNotification_Notification::NOTIFY_TYPE_WAREHOUSE;
                if ($lowStockNotificationId = $this->addProductToNotification($rule, $collection, $type, $warehouseId)) {
                    try {
                        /** add notification to inbox */
                        $this->addToInbox($rule, $lowStockNotificationId, $warehouseId);
                        /** @var  Magestore_Inventorysuccess_Model_LowStockNotification_Rule $ruleModel */
                        $ruleModel = Mage::getModel('inventorysuccess/lowStockNotification_rule');
                        $ruleModel->load($rule['rule_id']);
                        $ruleModel->setUpdatedAt(date('Y-m-d H:i:s'));
                        $ruleModel->save();
                    } catch (\Exception $e) {
                        return null;
                    }
                    $result[$warehouseId] = $lowStockNotificationId;
                }
            }
        }
        return $result;
    }

    /**
     * @param $salesPeriod
     * @return bool|string
     */
    public function getFromDateBySalePeriod($salesPeriod)
    {
        $fromDate = date('Y-m-d', strtotime('-' . $salesPeriod . ' days', strtotime(date('Y-m-d'))));
        return $fromDate;
    }

    /**
     * @param $rule
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     * @param $type
     * @param $warehouseId
     * @return null
     */
    public function addProductToNotification($rule, $collection, $type, $warehouseId)
    {
        $lowStockNotificationId = $this->createNewLowStockNotification($rule, $type, $warehouseId);
        if ($lowStockNotificationId) {
            try {
                $row = array();
                /** insert product to low stock notification */
                /** @var Mage_Catalog_Model_Product $col */
                foreach ($collection as $col) {
                    /** type low stock notification by product qty */
                    if ($rule['lowstock_threshold_type'] == Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_LOWSTOCK_THRESHOLD_PRODUCT_QTY) {
                        $rows[] = array(
                            'notification_id' => $lowStockNotificationId,
                            'product_id' => $col->getEntityId(),
                            'product_sku' => $col->getSku(),
                            'product_name' => $col->getName(),
                            'current_qty' => $col->getCurrentQty()
                        );
                    }
                    /** type low stock notification by sale day */
                    if ($rule['lowstock_threshold_type'] == Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_LOWSTOCK_THRESHOLD_SALE_DAY) {
                        $rows[] = array(
                            'notification_id' => $lowStockNotificationId,
                            'product_id' => $col->getEntityId(),
                            'product_sku' => $col->getSku(),
                            'product_name' => $col->getName(),
                            'current_qty' => $col->getCurrentQty(),
                            'sold_per_day' => $col->getSoldPerDay(),
                            'total_sold' => $col->getTotalSold(),
                            'availability_days' => $col->getAvailabilityDays(),
                            'availability_date' => $col->getAvailabilityDate()
                        );
                    }
                    if (count($rows) == $this->batchCount) {
                        $this->_getWriteAdapter()->insertMultiple(Mage::getSingleton('core/resource')->getTableName('os_lowstock_notification_product'), $rows);
                        $rows = array();
                    }
                }
                if (!empty($rows)) {
                    $this->_getWriteAdapter()->insertMultiple(Mage::getSingleton('core/resource')->getTableName('os_lowstock_notification_product'), $rows);
                }
                return $lowStockNotificationId;
            } catch (\Exception $e) {
                return null;
            }
        }
    }

    /**
     * @param $rule
     * @param $type
     * @param $warehouseId
     * @return int
     */
    public function createNewLowStockNotification($rule, $type, $warehouseId)
    {
        /** @var  Magestore_Inventorysuccess_Model_LowStockNotification_Notification $lowStockNotification */
        $lowStockNotification = Mage::getModel('inventorysuccess/lowStockNotification_notification');
        $warehouseName = '';
        if ($warehouseId) {
            /** @var  Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Collection $warehouseCollection */
            $warehouseCollection = Mage::getResourceModel('inventorysuccess/warehouse_collection');
            /** @var Magestore_Inventorysuccess_Model_Warehouse $warehouse */
            $warehouse = $warehouseCollection->addFieldToFilter('warehouse_id', $warehouseId)
                ->setCurPage(1)
                ->setPageSize(1)
                ->getFirstItem();
            if ($warehouse->getId()) {
                $warehouseName = $warehouse->getWarehouseName();
            }
        }
        $lowStockNotification->setRuleId($rule['rule_id'])
            ->setUpdateType($type)
            ->setNotifierEmails($rule['notifier_emails'])
            ->setLowstockThresholdType($rule['lowstock_threshold_type'])
            ->setLowstockThreshold($rule['lowstock_threshold'])
            ->setLowstockThresholdQty($rule['lowstock_threshold_qty'])
            ->setSalesPeriod($rule['sales_period'])
            ->setWarehouseId($warehouseId)
            ->setWarehouseName($warehouseName)
            ->setWarningMessage($rule['warning_message']);
        try {
            $lowStockNotification->save();
            return $lowStockNotification->getId();
        } catch (\Exception $e) {
            return null;
        }

    }

    /**
     * add notificaiton to inbox
     * @param $rule
     * @param $lowStockNotificationId
     * @param $warehouseId
     */
    public function addToInbox($rule, $lowStockNotificationId, $warehouseId)
    {
        /** @var  Mage_AdminNotification_Model_Inbox $adminInbox */
        $adminInbox = Mage::getModel('adminnotification/inbox');
        /** @var  Mage_Adminhtml_Model_Url $backendUrl */
        $backendUrl = Mage::getModel('adminhtml/url');
        $severity = Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE;
        $title = Mage::helper('inventorysuccess')->__('OS Low Stock Notifications for Global');
        $url = $backendUrl->getUrl('adminhtml/inventorysuccess_lowstocknotification_notification/edit', array('id' => $lowStockNotificationId));
        $description = $rule['description'] . ". " . $rule['warning_message']; // . '. You can check at '. $url;
        if ($warehouseId) {
            /** @var  Magestore_Inventorysuccess_Model_Warehouse $warehouse */
            $warehouse = Mage::getModel('inventorysuccess/warehouse');
            $warehouse->load($warehouseId);
            $warehouseName = $warehouse->getWarehouseName();
            $title = Mage::helper('inventorysuccess')->__("OS Low Stock Notifications for Warehouse: %s", $warehouseName);
        }
        $adminInbox->setData('severity', $severity)
            ->setData('title', $title)
            ->setData('description', $description)
            ->setData('url', $url);
        try {
            $adminInbox->save();
        } catch (\Exception $e) {
            return null;
        }
    }


    /**
     * @param $tempTables
     */
    protected function _removeTermTable($tempTables) {
            $sql = "DROP TABLE IF EXISTS " . Mage::getSingleton('core/resource')->getTableName($tempTables) . ";";
            $this->_getWriteAdapter()->query($sql);
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

}