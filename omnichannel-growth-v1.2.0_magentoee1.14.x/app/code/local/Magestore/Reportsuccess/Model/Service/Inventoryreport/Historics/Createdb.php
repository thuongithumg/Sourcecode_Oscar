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
 * Reportsuccess Magestore_Reportsuccess_Model_Service_Inventoryreport_Historics_Createdb
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
class Magestore_Reportsuccess_Model_Service_Inventoryreport_Historics_Createdb
{

        /**
         * @var
         */
        protected $targetDir;
        protected $warehouseIds;
        protected $list_file = array();
        protected $Connection ;

        /**
         * @var array
         */
        protected $ATTRIBUTE_CODE = array(
            'sku'=>'sku',
        );


    /**
     * Create backup and stream write to adapter
     *
     * @param Mage_Backup_Model_Backup $backup
     * @return Mage_Backup_Model_Db
     */
    public function createBackup(Mage_Backup_Model_Backup $backup, $wid,$wname)
    {
        $this->deletecsvData();
        $cron = Mage::getSingleton('reportsuccess/service_cron_reportindexer')
            ->isRunning(Magestore_Reportsuccess_Model_Service_Cron_Reportindexer::CACHE_TYPE_HISTORICS_REPORT);
        if($cron == 1){
            $count_wh = Magestore_Reportsuccess_Model_Mysql4_Costofgood_Collection::getWarehouseIdsxName();
            $this->_getAllfilesName();
            $i = 0;
            if($this->list_file){
                foreach($this->list_file as $time => $name){
                    if($this->timestamp($time) == $this->timestamp(time())){
                        /* really exist back_up data from cron */
                        $i++;
                    }
                }
            }
            if( count($count_wh)<= $i){
                return $this;
            }
        }
        $backup->open(true);
        $data = $this->historicsProcess($wid,$wname);
        $backup->write($data);
        $backup->close();
        return $this;
    }

    /**
     * @param $time
     * @return bool|string
     */
    public function timestamp($time){
        return date("Y-m-d", Mage::getModel('core/date')->timestamp($time));
    }
    /**
     * get all files name
     * @return array
     */
    public function _getAllfilesName(){
        $this->targetDir = Mage::getBaseDir('var') . DS . 'backups'. DS . 'reportsuccess';
        // foreach (glob($this->targetDir.'/*.*') as $file) {
        foreach (glob($this->targetDir.'/*.gz') as $file) {
            $filenames = str_replace($this->targetDir.'/','',$file);
            list($time, $type, $x) = explode("_", $filenames);
            $this->list_file[$time] = $time.'_'.$type;
        }
        return $this->list_file;
    }

    /**
     * @param $wid
     * @param $wname
     * @return string
     */
    public function historicsProcess($wid,$wname){

        $this->targetDir = Mage::getBaseDir("media") . DS . "reportsuccess";
        $cr = Mage::getSingleton('core/resource');
        $this->Connection = $cr->getConnection('core_write');
        //$connection = $this->_getConnection('read'); - use in resouceModel
        //$this->deletecsvData();
        return $this->prepareDataByCsv($wid,$wname);
    }
    /**
     * delete csv data
     */
    public function deletecsvData(){
        $all_files = $this->_getAllfilesName();
        $configTime =  Mage::getStoreConfig('reportsuccess/general/default_viewreport_apply_time');
        $applytime = $configTime ? $configTime : Magestore_Reportsuccess_Model_Stockreport_Applytime::_LAST_7_DAY;
        $count_day = Mage::getSingleton('reportsuccess/stockreport_applytime')->_options[$applytime];
        $list_to_del = array();
        for($i = 0; $i <= $count_day ; $i++){
            $filenames[] = Mage::getModel('core/date')->date('Y-m-d', '-'.$i.' days');
        }
        foreach($all_files as $key => $value){
            if(in_array($this->timestamp($key),$filenames)){
                continue;
            }else{
                $list_to_del[] = $value;
            }
        }
        $backupModel = Mage::getModel('reportsuccess/historics');
        if($list_to_del){
            foreach ($list_to_del as $id) {
                list($time, $type) = explode('_', $id);
                try {
                    $backupModel->loadByTimeAndType($time, $type);
                    if($backupModel->exists()){
                        $backupModel->deleteFile();
                    }
                }catch(Exception $e){
                    Mage::log($e->getMessage(),null,'log.log');
                }
            }
        }
    }
    /**
     * update data to view on csv file
     */
    public function prepareDataByCsv($wid,$wname){
        $connection = $this->Connection;
        $csv = "SKU ,Qty in warehouse ,Available Qty ,Warehouse ,Mac ,Inventory Value \n";//Column headers
        $select = $connection->select()->from(array('main_table' => Mage::getSingleton('core/resource')->getTableName('cataloginventory/stock_item')), array('product_id','total_qty','qty','warehouse_id'=>'stock_id'))
            ->where('stock_id = ?',$wid)
            ->joinLeft(array('mac' => Mage::getSingleton('core/resource')->getTableName('reportsuccess/costofgood')),
                'main_table.product_id = mac.product_id', array(
                    'mac'=>'IFNULL(mac.mac,0)','inv_value'=>'main_table.total_qty*(IFNULL(mac.mac,0))'
                )
            );
        $attributeCode = $this->ATTRIBUTE_CODE;
        foreach($attributeCode as $code => $value){
            $alias = $code . '_table';
            $attribute = Mage::getSingleton('eav/config')
                ->getAttribute(Mage_Catalog_Model_Product::ENTITY, $code);
                $select->join(
                    array($alias => $attribute->getBackendTable()),
                    "main_table.product_id = $alias.entity_id",
                    array($code => $value)
                );
        }
        $query = $connection->query($select);
        while ($row = $query->fetch()) {
            $csv.= $row['sku'].','.$row['total_qty'].','.$row['qty'].','.$wname.','.$row['mac'].','.$row['inv_value']. "\n";
        }
        return $csv;
    }

}
