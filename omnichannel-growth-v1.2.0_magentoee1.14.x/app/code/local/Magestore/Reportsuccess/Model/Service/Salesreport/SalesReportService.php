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
 *   @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Reportsuccess Magestore_Reportsuccess_Model_Service_Salesreport_SalesReportService
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
use Magestore_Reportsuccess_Model_Service_Mapping_Salesreport as Mapping;
class Magestore_Reportsuccess_Model_Service_Salesreport_SalesReportService
{
    protected $warehouseIds;
    protected $_listItems = array();
    protected $_listItemsToDelete = array();
    protected $Connection ;
    protected $_start = 0;
    protected $_totalSize = 0;
    protected $_check_cron;
    protected $_listItemsID_Of_ConfigProducts;

    const _LIMIT = 100;
    const _DATE = '2000-03-15 07:01:35';

    /**
     * convert Data
     */
    public function salesReportProcess($cron){
        $this->_check_cron = $cron;

        $warehouses = Mage::getResourceModel('inventorysuccess/warehouse_collection');
        $warehouseIds = array();
        foreach($warehouses as $key => $value){
            $warehouseIds[$value['warehouse_id']] = $value['warehouse_name'];
        }
        $this->warehouseIds = $warehouseIds;
        $cr = Mage::getSingleton('core/resource');
        $this->Connection = $cr->getConnection('core_write');

        /* check and get data to update */
        $items = $this->checkData();

        /* prepareData for items have parent_item */
        $configItems = $this->prepareDataOfConfigProduct();
        if(sizeof($configItems) > 0){
            /*prepareData for items simple*/
            $this->prepareData($configItems);
        }
        if(sizeof($items) > 0){
            /*prepareData for items simple*/
            $this->prepareData($items);
        }

        $queryAdapter = Magestore_Coresuccess_Model_Service::reportInventoryService()->sqlService();
        $table = Mage::getSingleton('core/resource')->getTableName(Mapping::_REPORT_TABLE);

        if(sizeof($this->_listItemsToDelete) > 0){
            $queryAdapter->deleteData($table,$this->_listItemsToDelete);
        }
        if(sizeof($this->_listItems) > 0){
            $queryAdapter->updateData($table,$this->_listItems);
        }
        /* calculated for loading ajax */
        return $this->returnResult();

    }

    /**
     * @return array
     */
    public function returnResult(){
        /* calculated for loading ajax */
        $remain_size_Session = Mage::getModel('admin/session')->getData(Mapping::_REMAIN_SIZE_SESSION);
        if(!$remain_size_Session)
            Mage::getModel('admin/session')->setData(Mapping::_REMAIN_SIZE_SESSION,$this->_totalSize);

        if($remain_size_Session && ($remain_size_Session < $this->_totalSize))
            Mage::getModel('admin/session')->setData(Mapping::_REMAIN_SIZE_SESSION,$this->_totalSize);

        $limit = ($this->_check_cron != null) ? (int)$this->_check_cron : self::_LIMIT;
        $remain_size = $this->_totalSize - $limit;
        $remain_size = ($remain_size > 0) ? $remain_size : 0;
        $update_time = $this->saveUpdateTime();
        $update_time = new DateTime($update_time);
        $returnData =  array(Mapping::_TOTAL_SIZE=>Mage::getModel('admin/session')->getData(Mapping::_REMAIN_SIZE_SESSION),
            Mapping::_REMAIN_SIZE => $remain_size,
            Mapping::_UPDATE_TIME => $update_time->format('M d, Y h:i:s')
        );
        return $returnData;
    }

    /**
     * @return array
     */
    public function checkData(){
        $limit = ($this->_check_cron != null) ? (int)$this->_check_cron : self::_LIMIT;
        $where = Mapping::_WHERE_CONDITION;
        $connection = $this->Connection;
        $sales_flat_order_item = Mage::getSingleton('core/resource')->getTableName(Mapping::_ORDER_ITEM_TABLE);
        $check_parent = "select product_type from {$sales_flat_order_item} where item_id = main_table.parent_item_id";
        $configuration_code = Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;

        $select = $connection->select()->
        from(array('main_table' => Mage::getSingleton('core/resource')->getTableName(Mapping::_ORDER_ITEM_TABLE)),
           /*cho nay de correct item cho configproduct
             check by parent_type != 0 -> return ko cho vào mảng items nữa,lưu lại item_id -> đến cuối duyệt lại = collection khác
           */
            array('*',
                  'parent_type' => "IF( ({$check_parent}) = '{$configuration_code}' , main_table.parent_item_id , 0 ) "))
        ->joinLeft(
            array('sales_report' => Mage::getSingleton('core/resource')->getTableName(Mapping::_REPORT_TABLE)),
            Mapping::_JOINLEFT_MAINTABLE_SALESREPORT_CONDITION,
            Mapping::_joinleft_maintable_salesreport_array())
            /* get Payment */
        ->joinLeft(
            array('payment_method' => Mage::getSingleton('core/resource')->getTableName(Mapping::_PAYMENT_TABLE)),
            Mapping::_JOINLEFT_MAINTABLE_PAYMENT_CONDITION,
            Mapping::_joinleft_maintable_payment_array())
            /* get Order information */
        ->joinLeft(
            array('order' => Mage::getSingleton('core/resource')->getTableName(Mapping::_ORDER_TABLE)),
            Mapping::_JOINLEFT_MAINTABLE_ORDER_CONDITION,
            Mapping::_joinleft_maintable_order_array())
            ->where("{$where} != 0")
            ->where("main_table.product_type = 'simple'")
            ->group('main_table.product_id')
            ->group('main_table.order_id')
            ->group('main_table.item_id')
            ->order('main_table.item_id')
            ->limit($limit);
            //->limit( self::_LIMIT,$this->_start);

        $this->_totalSize = $this->getTotalItems($select);

        $orderItems = $connection->query($select);
        $items = array();
        $items_id_of_configProduct = array();
        while ($row = $orderItems->fetch()) {
            if($row['parent_type'] != 0){
                $items_id_of_configProduct[] = $row['parent_type'];
                continue;
            }
            $items[] = $row;
        }

        $this->_listItemsID_Of_ConfigProducts = $items_id_of_configProduct;


        return $items;
    }

    public function prepareDataOfConfigProduct(){
         if(count($this->_listItemsID_Of_ConfigProducts)){
             $list_id = implode(",",$this->_listItemsID_Of_ConfigProducts);
             $connection = $this->Connection;
             $sales_flat_order_item = Mage::getSingleton('core/resource')->getTableName('sales_flat_order_item');
             $get_P_id_from_parent = "select product_id from {$sales_flat_order_item} where parent_item_id = main_table.item_id limit 1";
             $get_WH_id_from_parent = "select warehouse_id from {$sales_flat_order_item} where parent_item_id = main_table.item_id limit 1";
             $get_IT_id_from_parent = "select item_id from {$sales_flat_order_item} where parent_item_id = main_table.item_id limit 1";
             $update_at_from_simple = "select updated_at from {$sales_flat_order_item} where parent_item_id = main_table.item_id limit 1";
             $where = Mapping::_WHERE_CONDITION;
             $select = $connection->select()->
             from(array('main_table' => Mage::getSingleton('core/resource')->getTableName(Mapping::_ORDER_ITEM_TABLE)),
                 array('*',
                     'product_id' => "({$get_P_id_from_parent})",
                     'warehouse_id' => "({$get_WH_id_from_parent})",
                     'item_id' => "({$get_IT_id_from_parent})",
                     'updated_at' => "({$update_at_from_simple})",
                     'parent_type' => '0'))
                 ->joinLeft(
                     array('sales_report' => Mage::getSingleton('core/resource')->getTableName(Mapping::_REPORT_TABLE)),
                     Mapping::_JOINLEFT_MAINTABLE_SALESREPORT_CONDITION_CONFIG,
                     Mapping::_joinleft_maintable_salesreport_array())
                 /* get Payment */
                 ->joinLeft(
                     array('payment_method' => Mage::getSingleton('core/resource')->getTableName(Mapping::_PAYMENT_TABLE)),
                     Mapping::_JOINLEFT_MAINTABLE_PAYMENT_CONDITION,
                     Mapping::_joinleft_maintable_payment_array())
                 /* get Order information */
                 ->joinLeft(
                     array('order' => Mage::getSingleton('core/resource')->getTableName(Mapping::_ORDER_TABLE)),
                     Mapping::_JOINLEFT_MAINTABLE_ORDER_CONDITION,
                     Mapping::_joinleft_maintable_order_array())
                 ->where("{$where} != 0")
                 ->where("main_table.item_id in ({$list_id})")
                 ->group('main_table.product_id')
                 ->group('main_table.order_id')
                 ->group('main_table.item_id')
                 ->order('main_table.item_id');
             $orderItems = $connection->query($select);
             $items = array();
             while ($row = $orderItems->fetch()) {
                 $items[] = $row;
             }
             return $items;
         }
        return array();
    }

    /**
     * @return int
     */
    public function getLastUpdateSalesReport($flat = false){
        $type = Magestore_Reportsuccess_Helper_Data::SALESREPORT;
        $cr = Mage::getSingleton('core/resource');
        $this->Connection = $cr->getConnection('core_write');
        $connection = $this->Connection ;
        $select = $connection->select()->
        from(array('flag' => Mage::getSingleton('core/resource')->getTableName('os_flagtag_reindexer')),
            array('updated_at'))
            ->where('`report_type` = ?',$type);
        $query = $connection->query($select);
        $row = $query->fetch();
        $lifetime = isset($row['updated_at']) ? ($row['updated_at']) : self::_DATE;
        if($flat == true ){
            return $lifetime;
        }
        $updatedAt = new DateTime($lifetime);
        return $updatedAt->format('M d, Y h:i:s');
    }
    public function saveUpdateTime(){
        $queryAdapter = Magestore_Coresuccess_Model_Service::reportInventoryService()->sqlService();
        $table = Mage::getSingleton('core/resource')->getTableName('os_flagtag_reindexer');
        $updateTime = Mage::getModel('core/date')->date('Y-m-d H:i:s');
        if($this->getLastUpdateSalesReport(true) == self::_DATE){
            $item = array(
                'report_type' => Magestore_Reportsuccess_Helper_Data::SALESREPORT,
                'updated_at' => $updateTime
            );
            $items = array($item);
            $queryAdapter->updateData($table,$items);
        }else{
            $updateValues  = array(
                'updated_at' => $updateTime,
            );
            $field = 'report_type';
            $where = $table.'.'.$field." = '".Magestore_Reportsuccess_Helper_Data::SALESREPORT."' ";
            $queryAdapter->updateOne($table,$updateValues,$where);
        }
        return $updateTime;
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
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->columns(array('total' => 'COUNT(*)'));
        $connection = $this->Connection;
        $query = $connection->query($countSelect);
        $row = $query->fetch();
        return isset($row['total']) ? intval($row['total']) : 0;
    }

    /**
     * @param $items
     */
    public function prepareData($items){
        foreach($items as $item){
            $this->addDataToQuery($item);
            /* delete old data and insert only  - not update*/
            if($item['sales_report_id'] != 0){
               /* delete old data by where (item_id,product_id,order_id)  */
                $this->_listItemsToDelete[] = $item['items_to_delete'];
            }
        }
    }

    /**
     * @param $items
     * @return array
     */
    public function addDataToQuery($items){

        $where = " (product_id = {$items['product_id']} and order_item_id = {$items['item_id']}) ";
        $item_shipment = $this->getDataShipmentItem($where);
        $item_credit = $this->getDataCreditItem($where);

        $data['realized_sold_qty'] = max(0,$items['qty_shipped'] - $items['qty_refunded']);
        $data['potential_sold_qty'] = max(0,$items['qty_ordered'] - $items['qty_shipped'] - $items['qty_refunded'] - $items['qty_canceled']);

        if(sizeof($item_shipment) > 0){
            foreach($item_shipment as $warehouse => $qty){
                /* check cho tung warehouse de  tinh laij realized_sold_qty potential_sold_qty  */
                /* nếu return vào bất kì 1 warehouse nào mà ko phải warehouse order hay shipped -> sale = 0 */
                /* tính qty ship thực tế và dự đoán cẩn thận cho từng warehouse - chú ý đến warehouse đã order */
                if($warehouse != $items['warehouse_id']){
                    $qty_sold_realized = $qty;
                    if(isset($item_credit[$warehouse]) && $item_credit[$warehouse] != null ){
                       $qty_sold_realized = max(0,$qty-$item_credit[$warehouse]);
                    }
                    $data['realized_sold_qty'] = $data['realized_sold_qty'] - $qty_sold_realized;
                    $this->convertData($qty_sold_realized,$warehouse,$items,false);
                }
            }
        }
        return $this->convertData($data['realized_sold_qty'], $items['warehouse_id'], $items, true);
    }

    /**
     * @param $qty_sold_realized
     * @param $warehouse
     * @param $items
     * @param bool|false $primary_warehouse_ordered
     * @return array
     */
    public function convertData($qty_sold_realized,$warehouse,$items,$primary_warehouse_ordered = false){
        $data = array();
        $data['item_id'] = $items['item_id'];
        $data['order_id'] = $items['order_id'];
        $data['increment_id'] = $items['increment_id'];
        $data['product_id'] = $items['product_id'];
        $data['realized_sold_qty'] = $qty_sold_realized;
        if(!$primary_warehouse_ordered){
            $data['potential_sold_qty'] = 0;
        }else{
            $data['potential_sold_qty'] = max(0,$items['qty_ordered'] - $items['qty_shipped'] - $items['qty_refunded'] - $items['qty_canceled']);
        }
        $data['unit_cost'] = (isset($items['os_mac']) && $items['os_mac'] != 0) ? $items['os_mac'] : $items['base_cost'];
        $data['unit_cost'] =  $data['unit_cost'] ?  $data['unit_cost'] : 0;

        $data['unit_price'] = $items['base_price'] ? $items['base_price'] : 0 ;

        $data['unit_tax'] = $items['base_tax_amount']/$items['qty_ordered'];
        $data['unit_discount'] = $items['base_discount_amount']/$items['qty_ordered'];

        $data['unit_profit'] = $data['unit_price'] -  $data['unit_cost'] -  $data['unit_tax'] - $data['unit_discount'];
        $data['realized_cogs'] = $data['realized_sold_qty']*$data['unit_cost'];
        $data['potential_cogs'] = $data['potential_sold_qty']*$data['unit_cost'];
        $data['cogs'] = $data['realized_cogs'] + $data['potential_cogs'];
        $data['realized_profit'] = $data['unit_profit']*$data['realized_sold_qty'];
        $data['potential_profit'] = $data['unit_profit']*$data['potential_sold_qty'];
        $data['profit'] = $data['realized_profit'] + $data['potential_profit'];
        $data['realized_tax'] = $data['unit_tax']*$data['realized_sold_qty'];
        $data['potential_tax'] = $data['unit_tax']*$data['potential_sold_qty'];
        $data['tax'] = $data['realized_tax'] + $data['potential_tax'];
        $data['realized_discount'] = $data['unit_discount']*$data['realized_sold_qty'];
        $data['potential_discount'] = $data['unit_discount']*$data['potential_sold_qty'];
        $data['total_sale'] = $data['cogs'] + $data['profit'];
        $data['warehouse_id'] = $warehouse ;
        $data['status'] =  $items['status'];
        $data['shipping_method'] = $items['shipping_method'];
        $data['shipping_description'] = $items['shipping_description'];
        $data['payment_method'] = $items['payment_method'];
        $data['customer_group_id'] = $items['customer_group_id'];
        $data['customer_email'] = $items['customer_email'];
        $data['customer_firstname'] = $items['customer_firstname'] ? $items['customer_firstname'] : '';
        $data['customer_lastname'] = $items['customer_lastname'] ? $items['customer_lastname'] : '';
        $data['customer_middlename'] = $items['customer_middlename'] ? $items['customer_middlename'] : '';
        $data['created_at'] = $items['created_at'];
        $data['updated_at'] = $items['updated_at'];
        return $this->_listItems[] = $data;
    }

    /**
     * @param $where
     * @return array
     */
    public function getDataShipmentItem($where){
        $connection = $this->Connection;
        $selectShip = $connection->select()->
        from(array('main_table' => Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment_item')),
            array('qty','warehouse_id'))
            ->where("{$where}");
        $shipmentItems = $connection->query($selectShip);
        $item_shipment = array();
        while ($row = $shipmentItems->fetch()) {
            $item_shipment[$row['warehouse_id']] = $row['qty'];
        }
        return $item_shipment;
    }

    /**
     * @param $where
     * @return array
     */
    public function getDataCreditItem($where)
    {
        $connection = $this->Connection;
        $selectCredit = $connection->select()->
        from(array('main_table' => Mage::getSingleton('core/resource')->getTableName('sales_flat_creditmemo_item')),
            array('qty', 'warehouse_id'))
            ->where("{$where}");
        $creditItems = $connection->query($selectCredit);
        $item_credit = array();
        while ($row = $creditItems->fetch()) {
            $item_credit[$row['warehouse_id']] = $row['qty'];
        }
        return $item_credit;
    }


}