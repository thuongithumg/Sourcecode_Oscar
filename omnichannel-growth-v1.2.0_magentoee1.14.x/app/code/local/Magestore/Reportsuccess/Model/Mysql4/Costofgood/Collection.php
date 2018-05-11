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
 * Reportsuccess Resource Collection Model
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Status as Status;
class Magestore_Reportsuccess_Model_Mysql4_Costofgood_Collection extends
    Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('reportsuccess/costofgood');
    }
    
    /**
     * @var array
     */
    protected $MAPPING_FIELD = array(
        'total_inv_value' => 'IFNULL(costofgood.mac,0)*(SUM(main_table.total_qty))',
        'total_retail_value' => '`catalog_product_entity_decimal`.`value`*(SUM(main_table.total_qty))',
        'total_profit' => '`catalog_product_entity_decimal`.`value`*(SUM(main_table.total_qty)) - IFNULL(costofgood.mac,0)*(SUM(main_table.total_qty))',
        'total_profit_margin' => '(`catalog_product_entity_decimal`.`value`*(SUM(main_table.total_qty)) - IFNULL(costofgood.mac,0)*(SUM(main_table.total_qty)))/(`catalog_product_entity_decimal`.`value`*(SUM(main_table.total_qty)))*100',

        'total_in_coming_group' => 'SUM(main_table.qty_orderred - main_table.qty_returned - main_table.qty_transferred)',
        'total_in__coming_due_group'=> 'SUM(IF( (IFNULL(purchaseorder.expected_at,purchaseorder.created_at)- purchaseorder.created_at) < 0 ,(main_table.qty_orderred - main_table.qty_returned - main_table.qty_transferred),0))',
        'total_cost_group' => 'SUM((main_table.qty_orderred - main_table.qty_returned - main_table.qty_transferred)*main_table.cost)',
        'po_id_group' => 'GROUP_CONCAT( DISTINCT main_table.purchase_order_id SEPARATOR ",")',

    );
    /**
     * @var array
     */
    protected $MAPPING_FIELD_TO_FILTER = array(
        'mac' => 'mac',
        'total_inv_value' => 'IFNULL(mac,0)*(SUM(main_table.total_qty))',
        'total_retail_value' => '`catalog_product_entity_decimal`.`value`*(SUM(main_table.total_qty))',
        'total_profit' => '`catalog_product_entity_decimal`.`value`*(SUM(main_table.total_qty)) - IFNULL(mac,0)*(SUM(main_table.total_qty))',
        'total_profit_margin' => '(`catalog_product_entity_decimal`.`value`*(SUM(main_table.total_qty)) - IFNULL(mac,0)*(SUM(main_table.total_qty)))/(`catalog_product_entity_decimal`.`value`*(SUM(main_table.total_qty)))*100',
        'qty_in_order' => 'qty_in_po.qty_in_order',
        'qty_in_due' => 'qty_in_po.qty_in_due',
        'total_in_coming_group' => 'SUM(main_table.qty_orderred - main_table.qty_returned - main_table.qty_transferred)',
        'total_in__coming_due_group' => 'SUM(IF( (IFNULL(purchaseorder.expected_at,purchaseorder.created_at)- purchaseorder.created_at) < 0 ,(main_table.qty_orderred - main_table.qty_returned - main_table.qty_transferred),0))',
        'total_cost_group'=> 'SUM((main_table.qty_orderred - main_table.qty_returned - main_table.qty_transferred)*main_table.cost)',
    );

    /**
     * Return warehouse product collection with product information and total qtys
     *
     * @return Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Product_Collection
     */
    public function getAllStocksWithProductInformation()
    {
        try{
            $collection =  Mage::getResourceModel('inventorysuccess/warehouse_product_collection')
                ->joinProductCollection()
                ->calculateQtys();
        }catch (Exception $e){
            echo $e->getMessage(); die;
        }
        return $collection;
    }

    /**
     * @param $collection
     * @return mixed
     */
    public function getInventoryreportCollection($collection){
        try{
            $collection->getSelect()->joinLeft(
                array('costofgood' => $collection->getTable('reportsuccess/costofgood')), 'main_table.product_id = costofgood.product_id',
                array('mac'=>"IFNULL(mac,0)")
            );
            $collection->getSelect()->columns(array(
                'total_inv_value' => new Zend_Db_Expr($this->MAPPING_FIELD['total_inv_value']),
                'total_retail_value' => new Zend_Db_Expr($this->MAPPING_FIELD['total_retail_value']),
                'total_profit' => new Zend_Db_Expr($this->MAPPING_FIELD['total_profit']),
                'total_profit_margin' => new Zend_Db_Expr('FORMAT('.$this->MAPPING_FIELD['total_profit_margin'].',2)'),
            ));
        }catch (Exception $e){
            echo $e->getMessage(); die;
        }
        return $collection;
    }

    /**
     * @param null $primarycollection
     * @return null
     */
    public function getDetailsreportCollection($primarycollection = null){
        try{
            /* if not install PurchaseOrder */
            if(!Mage::helper('reportsuccess')->purchaseInstalled() && $primarycollection){
                $primarycollection->getSelect()->columns(array(
                    'qty_in_order' => new Zend_Db_Expr('0'),
                ));
                return $primarycollection;
            }

            $array = array(Status::STATUS_COMPLETED, Status::STATUS_CANCELED);
            $collection = Mage::getResourceModel('purchaseordersuccess/purchaseorder_item_collection');
            $collection->getSelect()->joinLeft(
                array('purchaseorder' => $collection->getTable('purchaseordersuccess/purchaseorder')), 'main_table.purchase_order_id = purchaseorder.purchase_order_id',
                array('status'=>"purchaseorder.status",
                    'supplier_id'=>'purchaseorder.supplier_id',
                    'totaL_cost'=>'(main_table.qty_orderred - main_table.qty_returned - main_table.qty_transferred)*main_table.cost',
                    'order_created_at'=>'purchaseorder.created_at',
                    'order_expected_at'=>'purchaseorder.expected_at')
            );
            $now = Mage::getModel('core/date')->date('Y-m-d');
            $collection->getSelect()->columns(array(
                'total_in_order' => new Zend_Db_Expr('(main_table.qty_orderred - main_table.qty_returned - main_table.qty_transferred)'),
                //'total_in_due'=> new Zend_Db_Expr(' IF( (IFNULL(purchaseorder.expected_at,purchaseorder.created_at)- purchaseorder.created_at) < 0 ,(main_table.qty_orderred - main_table.qty_returned - main_table.qty_transferred),0)')
                'total_in_due'=> new Zend_Db_Expr(' IF(TIMESTAMPDIFF(SECOND,IFNULL(purchaseorder.expected_at,\'2050-03-15\'),"'.$now.'") > 0 ,(main_table.qty_orderred - main_table.qty_returned - main_table.qty_transferred),0)')
            ));
            $collection->getSelect()->where('purchaseorder.status not in (?)',$array);
            $collection->getSelect()->where('purchaseorder.type = (?)',Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Type::TYPE_PURCHASE_ORDER);

                if ($primarycollection){
                        $this->createTempTable('report_inventory_temp_table',$collection->getSelect()->__toString());
                        $collectionData = ' SELECT product_id , sum(`total_in_order`) as `qty_in_order`  ,sum(`total_in_due`) as `qty_in_due` FROM `report_inventory_temp_table` group by `product_id`';
                        $this->createTable('stock_purchase_order_information',$collectionData);

                        $primarycollection->getSelect()->joinLeft(
                            array('qty_in_po' => 'stock_purchase_order_information'), 'main_table.product_id = qty_in_po.product_id',
                            array('qty_in_order'=>"qty_in_po.qty_in_order",
                                'qty_in_due'=>"qty_in_po.qty_in_due")
                        );
                        return $primarycollection;
                }else{
                        $collection->getSelect()->columns(array(
                            'total_in_coming_group' => new Zend_Db_Expr($this->MAPPING_FIELD['total_in_coming_group']),
                            //'total_in__coming_due_group'=> new Zend_Db_Expr($this->MAPPING_FIELD['total_in__coming_due_group']),
                            'total_in__coming_due_group'=> new Zend_Db_Expr('SUM(IF( TIMESTAMPDIFF(SECOND,IFNULL(purchaseorder.expected_at,\'2050-03-15\'),"'.$now.'") > 0 ,
                                 (main_table.qty_orderred - main_table.qty_returned - main_table.qty_transferred),0))'),
                            'total_cost_group' => new Zend_Db_Expr($this->MAPPING_FIELD['total_cost_group']),
                            'po_id_group' => new Zend_Db_Expr($this->MAPPING_FIELD['po_id_group']),
                            ));
                        $collection->getSelect()->group('main_table.product_id');
                        $collection->getSelect()->group('purchaseorder.supplier_id');

                        return $collection;
                }
        }
        catch(Exception $e){
            echo $e->getMessage(); die;
        }
    }

    /**
     * @param $collection
     * @return null
     */
    public function getLocationreportCollection($collection){

        $Invcollection = $this->getInventoryreportCollection($collection);
        $collection = $this->getDetailsreportCollection($Invcollection);
        $ids = $this->getWarehouseIds();
        foreach($ids as $id){
            $collection->getSelect()->columns(array(
                'available_qty_'.$id => new Zend_Db_Expr('SUM(IF( main_table.stock_id = '.$id.',main_table.qty,0))'),
                'sum_qty_to_ship_'.$id => new Zend_Db_Expr('SUM(IF( main_table.stock_id = '.$id.',main_table.total_qty - main_table.qty,0))'),
                'sum_total_qty_'.$id => new Zend_Db_Expr('SUM(IF( main_table.stock_id = '.$id.',main_table.total_qty,0))'),
                //'qty_in_order_'.$id => new Zend_Db_Expr('(IF( main_table.stock_id = '.$id.',qty_in_po.qty_in_order,0))'),
                'total_inv_value_'.$id => new Zend_Db_Expr('(IF( main_table.stock_id = '.$id.','.$this->MAPPING_FIELD['total_inv_value'].',0))'),
                'total_retail_value_'.$id => new Zend_Db_Expr('(IF( main_table.stock_id = '.$id.','.$this->MAPPING_FIELD['total_retail_value'].',0))'),
                'total_profit_'.$id => new Zend_Db_Expr('(IF( main_table.stock_id = '.$id.','.$this->MAPPING_FIELD['total_profit'].',0))'),
                'total_profit_margin_'.$id => new Zend_Db_Expr('(IF( main_table.stock_id = '.$id.','.$this->MAPPING_FIELD['total_profit_margin'].',0))'),
            ));
            if(Mage::helper('reportsuccess')->purchaseInstalled()){
                $collection->getSelect()->columns(array(
                    'qty_in_order_'.$id => new Zend_Db_Expr('(IF( main_table.stock_id = '.$id.',qty_in_po.qty_in_order,0))'),
                ));
            }
        }
        return $collection;
    }

    /**
     * @param $warehouseId
     * @param $collection
     * @return mixed
     */
    public function getHistoricsreportCollection($warehouseId,$collection){

        $collection = Mage::getResourceModel('reportsuccess/historics_collection');

        /* join attribute code */
        Mage::helper('reportsuccess')->service()->attributeMapping($collection);

        $date = Mage::getModel('admin/session')->getData('date_session');
        if(!$date){
            $date = Mage::getSingleton('reportsuccess/service_inventoryreport_modifigrids_modifigrids')->temp_date();
        }
        $collection->addFieldToFilter('main_table.updated_at',$date);

        if(!$warehouseId || ($warehouseId == Magestore_Reportsuccess_Helper_Data::ALL_WAREHOUSE) ){
            //$collection->addFieldToFilter('main_table.warehouse_id',null);
            /* not update warehouse_id = null in this table for all_warehouse selected , instead group by product_id and get sum qty from all product_id */
            $collection->getSelect()->columns(array(
                'total_qty' => new Zend_Db_Expr('Sum(main_table.total_qty)'),
                'inv_value' => new Zend_Db_Expr('Sum(main_table.inv_value)'),
            ));
        }else{
            $collection->addFieldToFilter('warehouse_id',$warehouseId);
        }
        $collection->getSelect()->group('main_table.product_id');
        //$collection->getSelect()->reset(Zend_Db_Select::GROUP);

        return $collection;
    }

    /**
     * @return mixed
     */
    public function getWarehouseIds(){
        /* permission list warehouse */
        $collection = Mage::getModel('inventorysuccess/warehouse')->getCollection();
        $collection = Magestore_Coresuccess_Model_Service::permissionService()->filterPermission(
            $collection, 'admin/reportsuccess/report/inventoryreport'
        );
        $ids = $collection->getColumnValues('warehouse_id');
        //zend_debug::dump($collection->getItems());
        return $ids;
    }

    /**
     * @return array
     */
    static function getWarehouseIdsxName(){
        $warehouse =  Mage::getModel('inventorysuccess/warehouse')->getCollection();
        $array = array();
        foreach($warehouse as $key => $value){
            $array[$value['warehouse_id']] = $value['warehouse_name'];
        }
        return $array;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getWarehouseName($id){
        $warehouse =  Mage::getModel('inventorysuccess/warehouse')->load($id);
        return $warehouse->getData('warehouse_name');
    }

    /**
     * @param $tempTableArr
     */
    protected function removeTempTables($tempTableArr) {
        $coreResource = Mage::getSingleton('core/resource');
        $sql = "";
        $sql .= "DROP TABLE  IF EXISTS " . $coreResource->getTableName($tempTableArr) . ";";
        $coreResource->getConnection('core_write')->query($sql);
        return;
    }

    /**
     * @param $tempTable
     * @param $collection
     */
    protected function createTempTable($tempTable, $collection) {
        self::removeTempTables($tempTable);
        $coreResource = Mage::getSingleton('core/resource');
        $_temp_sql = "CREATE TEMPORARY TABLE " . $coreResource->getTableName($tempTable) . " ("; // CREATE TEMPORARY TABLE
        $_temp_sql .= $collection. ");";
        $coreResource->getConnection('core_write')->query($_temp_sql);
    }

    /**
     * @param $tempTable
     * @param $collection
     */
    protected function createTable($tempTable, $collection) {
        self::removeTempTables($tempTable);
        $coreResource = Mage::getSingleton('core/resource');
        $_temp_sql = "CREATE TABLE " . $coreResource->getTableName($tempTable) . " ("; // CREATE TEMPORARY TABLE
        $_temp_sql .= $collection. ");";
        $coreResource->getConnection('core_write')->query($_temp_sql);
    }

    /**
     * @param $collection
     * @param $columnName
     * @param $filterValue
     * @return mixed
     */
    public function filterInventoryCallback($collection,$columnName,$filterValue){
        if (isset($filterValue['from'])) {
            $collection->getSelect()->having($this->MAPPING_FIELD_TO_FILTER[$columnName] . ' >= ?', $filterValue['from']);
        }
        if (isset($filterValue['to'])) {
            $collection->getSelect()->having($this->MAPPING_FIELD_TO_FILTER[$columnName] . ' <= ?', $filterValue['to']);
        }
        return $collection;
    }
}