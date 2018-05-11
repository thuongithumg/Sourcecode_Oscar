<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 *   @category    Magestore
 *   @package     Magestore_Reportsuccess
 *   @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Reportsuccess Resource Collection Model
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */

class Magestore_Debugsuccess_Model_Mysql4_Wrongqty_Collection extends
    Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $TOTAL;
    protected $SIZE;
    protected $_ALL_WAREHOUSE_IDS;
    /**
     * @var array
     */
    protected $ATTRIBUTE_CODE = array(
        'sku'=>'sku',
        'name'=>'value',
    );
    /**
     * @var array
     */
    protected $MAPPING_FIELD_TO_FILTER = array(
        'on_hold_qty' =>  'IFNULL(os_debug_pending_orders_items.on_hold_qty,0)',
        'product_id' => 'main_table.product_id',
    );

    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('debugsuccess/wrongqty');

        $warehouses = Mage::getResourceModel('inventorysuccess/warehouse_collection');
        $this->_ALL_WAREHOUSE_IDS = $warehouses->getAllIds();
    }
    /**
     * @return mixed
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        // Count doesn't work with group by columns keep the group by
        if(count($this->getSelect()->getPart(Zend_Db_Select::GROUP)) > 0) {
            $countSelect->reset(Zend_Db_Select::GROUP);
            $countSelect->distinct(true);
            $group = $this->getSelect()->getPart(Zend_Db_Select::GROUP);
            $countSelect->columns("COUNT(DISTINCT ".implode(", ", $group).")");
        } else {
            $countSelect->columns('COUNT(*)');
        }
        return $countSelect;
    }

    /**
     * @return Magestore_Debugsuccess_Model_Mysql4_Wrongqty_Collection
     */
    public function getAllStockWrongQtyCollection(){

        $warehouseIds = $this->_ALL_WAREHOUSE_IDS;
        $collection = $this;

        $collection->addFieldToSelect('product_id');
        $collection->addFieldToSelect('is_in_stock');
        $collection->addFieldToSelect('qty');

        $attributeCode = $this->ATTRIBUTE_CODE;
        foreach($attributeCode as $code => $value){
            $alias = $code . '_table';
            $attribute = Mage::getSingleton('eav/config')
                ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $code);
            if($code == 'name'){
                $collection->getSelect()->join(
                    array($alias => $attribute->getBackendTable()),
                    "main_table.product_id = $alias.entity_id AND $alias.attribute_id={$attribute->getId()} AND $alias.store_id = 0",
                    array($code => $value)
                );
            }else{
                $collection->getSelect()->join(
                    array($alias => $attribute->getBackendTable()),
                    "main_table.product_id = $alias.entity_id",
                    array($code => $value)
                );
            }
        }

        /* Start : return correct on_hold_qty in system */
        $this->getPendingOrderItems();
        $collection->getSelect()->joinLeft(array('os_debug_pending_orders_items' => Mage::getSingleton('core/resource')->getTableName('os_debug_pending_orders_items')),
            'os_debug_pending_orders_items.product_id = main_table.product_id',
            array(
                'on_hold_qty' => 'IFNULL(os_debug_pending_orders_items.on_hold_qty,0)',
            )
        );
        /* End : return correct on_hold_qty in system */

        /* Start : qty values in per warehouse */
        foreach($warehouseIds as $id){
            $collection->getSelect()->columns(array(
                'available_qty_'.$id => new Zend_Db_Expr('SUM(IF( main_table.stock_id = '.$id.',main_table.qty,0))'),
                'sum_qty_to_ship_'.$id => new Zend_Db_Expr('SUM(IF( main_table.stock_id = '.$id.',main_table.total_qty - main_table.qty,0))'),
                'sum_total_qty_'.$id => new Zend_Db_Expr('SUM(IF( main_table.stock_id = '.$id.',main_table.total_qty,0))')
            ));
        }
        /* End : qty values in per warehouse */

        $collection->getSelect()->group('main_table.product_id');


        /* get all products wrong qty to filter */
        $id_wrongs = $this->getIdsWrongQty();
        Mage::getModel('admin/session')->setData('session_list_wrong_qty',$id_wrongs);

        if(empty($id_wrongs)){
            $id_wrongs[] = 0;
        }
        $collection->getSelect()->where('main_table.product_id IN(?)',$id_wrongs);
        return $collection;
    }

    /**
     * @return array
     */
    public function getIdsWrongQty(){
        $cr = Mage::getSingleton('core/resource');
        $connection = $cr->getConnection('core_write');

        /* Start : prepare temp_table  */
        $select = $connection->select()->from(array('main_table' => $this->getTable('cataloginventory/stock_item')), array(
            'product_id',
            'Sum(IF(stock_id = 1,qty,0)) as catalog_qty',
            'Sum(IF(stock_id = 1,0,qty)) as total_avail_qty_in_all_warehouse',
            'Sum(IF(stock_id = 1,0,total_qty) - IF(stock_id = 1,0,qty)) as total_on_hold_in_all_warehouse'
        ))
            ->group('main_table.product_id');

        $select->joinLeft(array('os_debug_pending_orders_items' => Mage::getSingleton('core/resource')->getTableName('os_debug_pending_orders_items')),
            'os_debug_pending_orders_items.product_id = main_table.product_id',
            array(
                'catalog_on_hold_qty' => 'IFNULL(os_debug_pending_orders_items.on_hold_qty,0)',
            )
        );

        /* End : prepare temp_table */

        $queryAdapter = Mage::getResourceModel('debugsuccess/wrongqty');
        $queryAdapter->createTable('os_warehouse_debug_data',$select,true);

        /* Start : detect products that mismatch qty */
        $query = $connection->select()->from(array('main_table' => Mage::getSingleton('core/resource')->getTableName('os_warehouse_debug_data')), array(
            'product_id'
        ))->where('( (catalog_qty != total_avail_qty_in_all_warehouse)
                      OR (catalog_on_hold_qty !=  total_on_hold_in_all_warehouse) )');
        /* End : detect products that mismatch qty */

        $productIds = $connection->query($query);
        $items = array();
        while ($row = $productIds->fetch()) {
            $items[] = $row['product_id'];
        }
        return $items;
    }

    /**
     *
     */
    public function getPendingOrderItems(){
        $cr = Mage::getSingleton('core/resource');
        $connection = $cr->getConnection('core_write');

        $order_item_table = $this->getTable('sales/order_item');
        $on_hold_qty_sql = " (select (qty_ordered-qty_shipped-qty_refunded-qty_canceled) from {$order_item_table} where item_id = main_table.parent_item_id) ";
        $primaryField = "main_table.parent_item_id";
        $foreignField = "(select product_type from {$order_item_table} where item_id = main_table.parent_item_id)";
        $on_hold_only_simple = "main_table.qty_ordered - main_table.qty_shipped - main_table.qty_refunded - main_table.qty_canceled";

        $configuration_code = Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;

        /*  Start SQL : select all simple products and group by product_id , if exist parent_item_id -> calculate in configuration products */
        $select = $connection->select()->from(array('main_table' => $this->getTable('sales/order_item')), array(
            'product_id',
            //'Sum( IF(('.$primaryField.' > 0 && '.$foreignField.' = "'.$configuration_code.'" ), IF('.$on_hold_qty_sql.' > 0, '.$on_hold_qty_sql.', 0), IF('.$on_hold_only_simple.' > 0, '.$on_hold_only_simple.', 0) ) ) as on_hold_qty',
            'Sum( IF(('.$foreignField.' = "'.$configuration_code.'" ), IF('.$on_hold_qty_sql.' > 0, '.$on_hold_qty_sql.', 0), IF('.$on_hold_only_simple.' > 0, '.$on_hold_only_simple.', 0) ) ) as on_hold_qty',
        ))
        ->where('product_type = ?', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
        ->group('main_table.product_id');
        /* End SQL */

        /* Start : create table that show product_id and correct on_hold_qt in system */
        $PendingOrderItems = Mage::getResourceModel('debugsuccess/wrongqty');
        $PendingOrderItems->dropTable(Mage::getSingleton('core/resource')->getTableName('os_debug_pending_orders_items'),false);
        $PendingOrderItems->createTable(Mage::getSingleton('core/resource')->getTableName('os_debug_pending_orders_items'),$select,false);
        /* End : create table that show product_id and correct on_hold_qt in system */
        return;
    }


    /**
     * @param $ids
     * @param $warehouse_id
     */
    public function getPendingOrderItemsByWarehouse($ids,$warehouse_id){
        $cr = Mage::getSingleton('core/resource');
        $connection = $cr->getConnection('core_write');

        $order_item_table = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
        $on_hold_qty_sql = " (select (qty_ordered-qty_shipped-qty_refunded-qty_canceled) from {$order_item_table} where item_id = main_table.parent_item_id) ";
        $primaryField = "main_table.parent_item_id";
        $foreignField = "(select product_type from {$order_item_table} where item_id = main_table.parent_item_id)";
        $on_hold_only_simple = "main_table.qty_ordered - main_table.qty_shipped - main_table.qty_refunded - main_table.qty_canceled";

        $configuration_code = Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;

        /*  Start SQL : select all simple products and group by product_id , if exist parent_item_id -> calculate in configuration products */
        $select = $connection->select()->from(array('main_table' => $order_item_table), array(
            'product_id',
            'Sum( IF(('.$foreignField.' = "'.$configuration_code.'" ), IF('.$on_hold_qty_sql.' > 0, '.$on_hold_qty_sql.', 0), IF('.$on_hold_only_simple.' > 0, '.$on_hold_only_simple.', 0) ) ) as on_hold_qty_'.$warehouse_id,
        ))
            ->where('product_type = ?', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
            ->where('product_id in (?)', $ids)
            ->where('warehouse_id = ?', $warehouse_id)
            ->group('main_table.product_id');
        /* End SQL */

        /* Start : create temp table that show product_id and correct on_hold_qt in this warehouse */
        $PendingOrderItems = Mage::getResourceModel('debugsuccess/wrongqty');
        $PendingOrderItems->dropTable(Mage::getSingleton('core/resource')->getTableName('os_debug_pending_orders_items_'.$warehouse_id),false);
        $PendingOrderItems->createTable(Mage::getSingleton('core/resource')->getTableName('os_debug_pending_orders_items_'.$warehouse_id),$select,true);
        /* End : create table that show product_id and correct on_hold_qt in this warehouse*/
    }

    /**
     * @param $ids
     * @return mixed
     */
    public function getprepareCollectionToCollect($ids){
        $Collection = Mage::getResourceModel('cataloginventory/stock_item_collection');
        $Collection->addFieldToSelect(array('item_id','product_id','qty','total_qty'));
        $Collection->addFieldToFilter('main_table.product_id',array('in'=>$ids));

        $Collection->getSelect()->joinLeft(array('os_debug_pending_orders_items' => Mage::getSingleton('core/resource')->getTableName('os_debug_pending_orders_items')),
            'os_debug_pending_orders_items.product_id = main_table.product_id',
            array(
                'on_hold_qty' => 'IFNULL(os_debug_pending_orders_items.on_hold_qty,0)',
            )
        );
        /* Start : qty values in per warehouse */
        foreach($this->_ALL_WAREHOUSE_IDS as $w_id){
            $Collection->getSelect()->columns(array(
                'available_qty_'.$w_id => new Zend_Db_Expr('SUM(IF( main_table.stock_id = '.$w_id.',main_table.qty,0))'),
                'sum_qty_to_ship_'.$w_id => new Zend_Db_Expr('SUM(IF( main_table.stock_id = '.$w_id.',main_table.total_qty - main_table.qty,0))'),
                'sum_total_qty_'.$w_id => new Zend_Db_Expr('SUM(IF( main_table.stock_id = '.$w_id.',main_table.total_qty,0))'),
                'item_id'.$w_id => new Zend_Db_Expr('SUM(IF( main_table.stock_id = '.$w_id.',main_table.item_id,0))'),
            ));
        }
        $Collection->getSelect()->group('main_table.product_id');
        /* End : qty values in per warehouse */

        foreach($this->_ALL_WAREHOUSE_IDS as $warehouse_id){
            $this->getPendingOrderItemsByWarehouse($ids,$warehouse_id);

            $Collection->getSelect()->joinLeft(array('os_debug_pending_orders_items_'.$warehouse_id => Mage::getSingleton('core/resource')->getTableName('os_debug_pending_orders_items_'.$warehouse_id)),
                'os_debug_pending_orders_items_'.$warehouse_id.'.product_id = main_table.product_id',
                array(
                    'on_hold_qty_'.$warehouse_id => 'IFNULL(os_debug_pending_orders_items_'.$warehouse_id.'.on_hold_qty_'.$warehouse_id.',0)',
                )
            );
        }
        return $Collection;

    }


    /**
     * @param $collection
     * @param $columnName
     * @param $filterValue
     * @return mixed
     */
    public function filterDebugCallback($collection,$columnName,$filterValue){
        if (isset($filterValue['from'])) {
            $collection->getSelect()->where($this->MAPPING_FIELD_TO_FILTER[$columnName] . ' >= ?', $filterValue['from']);
        }
        if (isset($filterValue['to'])) {
            $collection->getSelect()->where($this->MAPPING_FIELD_TO_FILTER[$columnName] . ' <= ?', $filterValue['to']);
        }
        return $collection;
    }

    /**
     * @param $select
     * @return int
     */
    public function getTotalItems($select)
    {
        $countSelect = clone $select;
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->columns(array('total' => 'COUNT(*)'));
        $cr = Mage::getSingleton('core/resource');
        $connection = $cr->getConnection('core_write');
        $query = $connection->query($countSelect);
        $row = $query->fetch();
        return isset($row['total']) ? intval($row['total']) : 0;
    }

    /**
     * @param $select
     */
    public function viewData($select){
        $cr = Mage::getSingleton('core/resource');
        $connection = $cr->getConnection('core_write');
        $query = $connection->fetchAll($select);
    }

    /**
     * @param $ids
     * @return array
     */
    public function getOrderItemNoneWarehouse($ids){
        $cr = Mage::getSingleton('core/resource');
        $connection = $cr->getConnection('core_write');
        $order_item_table = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
        $select = $connection->select()->from(array('main_table' => $order_item_table), array(
            'product_id',
            'order_ids' => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT main_table.order_id SEPARATOR ",")'),
            'item_ids' => new Zend_Db_Expr('GROUP_CONCAT(DISTINCT main_table.item_id SEPARATOR ",")'),
        ))
            ->where('product_type = ?', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
            ->where('product_id in (?)', $ids)
            ->where('warehouse_id = ?', 0)
        ->group('main_table.product_id');
        /* End SQL */

        $orderItems = $connection->query($select);
        $items = array();
        while ($row = $orderItems->fetch()) {
            $items[] = $row;
        }
        return $items;
    }

}