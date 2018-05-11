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
 * Reportsuccess Magestore_Reportsuccess_Model_Service_Inventoryreport_Historics_HistoricsService
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
class Magestore_Reportsuccess_Model_Service_Inventoryreport_Historics_HistoricsService
{

    /**
     * @var
     */
    protected $targetDir;
    protected $warehouseIds;
    protected $list_file = array();
    protected $Connection ;

    /**
     * delete and update stock historics data
     */
    public function historicsProcess($cron){
        $warehouses = Mage::getResourceModel('inventorysuccess/warehouse_collection');
        $warehouseIds = array();
        foreach($warehouses as $key => $value){
            $warehouseIds[$value['warehouse_id']] = $value['warehouse_name'];
        }
        $this->targetDir = Mage::getBaseDir("media") . DS . "reportsuccess";
        $this->warehouseIds = $warehouseIds;

        $cr = Mage::getSingleton('core/resource');
        $this->Connection = $cr->getConnection('core_write');
        //$connection = $this->_getConnection('read'); - use in resouceModel
        if(Mage::getStoreConfig('reportsuccess/general/defaul_viewreport_type') == Magestore_Reportsuccess_Model_Stockreport_Viewreport::EXCELL){
           if($cron){
               $name = 'inventoryreport-'.date("Y-m-d", Mage::getModel('core/date')->timestamp(time())).'.csv';
               if(file_exists($this->targetDir.'/'.$name)){
                   return true;
               }
           }
            $this->deletecsvData();
            $this->prepareDataByCsv();
        }else{
            $this->deleteData();
            $this->prepareData();
        }
    }

    /**
     * delete csv data
     */
    public function deletecsvData(){
        $all_files = $this->getAllfilesName();
        $configTime =  Mage::getStoreConfig('reportsuccess/general/default_viewreport_apply_time');
        $applytime = $configTime ? $configTime : Magestore_Reportsuccess_Model_Stockreport_Applytime::_LAST_7_DAY;
        $count_day = Mage::getSingleton('reportsuccess/stockreport_applytime')->_options[$applytime];
            for($i = 1; $i <= $count_day ; $i++){
                $filenames[] = 'inventoryreport-'.Mage::getModel('core/date')->date('Y-m-d', '-'.$i.' days').'.csv';
            }
        foreach($all_files as $key => $value){
                if(in_array($key,$filenames))
                    continue;

                if(file_exists($this->targetDir.'/'.$key))
                $this->deletefiles($key);
        }
    }

    /**
     * delete grid data
     */
    public function deleteData(){
        try {
            $defaultime = '7 days';
            $time = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
            if ($applytime = Mage::getStoreConfig('reportsuccess/general/default_viewreport_apply_time')) {
                if ($applytime == Magestore_Reportsuccess_Model_Stockreport_Applytime::_LAST_7_DAY)
                    $defaultime = '7 days';

                if ($applytime == Magestore_Reportsuccess_Model_Stockreport_Applytime::_LAST_30_DAY)
                    $defaultime = '1 months';

                if ($applytime == Magestore_Reportsuccess_Model_Stockreport_Applytime::_LAST_3_MONTH)
                    $defaultime = '3 months';
            }
            $delete_date = Mage::getModel('core/date')->date('Y-m-d', '-' . $defaultime);
            $connection = $this->Connection;
            $connection->beginTransaction();
            $field = 'updated_at';
            $connection->delete(
                //$this->getTable('reportsuccess/historics'),
                Mage::getSingleton('core/resource')->getTableName('reportsuccess/historics'),
                $connection->quoteInto(Mage::getSingleton('core/resource')->getTableName('reportsuccess/historics') . '.' . $field . ' <= (?) ', $delete_date)
            );
            $connection->delete(
                Mage::getSingleton('core/resource')->getTableName('reportsuccess/historics'),
                $connection->quoteInto(Mage::getSingleton('core/resource')->getTableName('reportsuccess/historics') . '.' . $field . ' = (?) ', $time)
            );
            $connection->commit();
        }catch (Exception $e){
            $connection->rollBack();
            zend_debug::dump($e->getMessage());
            die;
        }
    }

    /**
     * update data to view on Grid
     */
    public function prepareData(){
        try{
            $time = date("Y-m-d", Mage::getModel('core/date')->timestamp(time()));
            $connection = $this->Connection;

            $select = $connection->select()->from(array('main_table' => Mage::getSingleton('core/resource')->getTableName('cataloginventory/stock_item')), array('product_id','total_qty'=>'total_qty','warehouse_id'=>'stock_id'))
                ->where('stock_id != ?',1)
                ->joinLeft(array('mac' => Mage::getSingleton('core/resource')->getTableName('reportsuccess/costofgood')),
                    'main_table.product_id = mac.product_id', array(
                        'mac'=>'IFNULL(mac.mac,0)','inv_value'=>'main_table.total_qty*(IFNULL(mac.mac,0))'
                    )
                );
            $query = $connection->query($select);
            $i = 1;
            $items = array();
            while ($row = $query->fetch()) {
                //zend_debug::dump($row);
                $row['updated_at'] = $time;
                $items[] = $row;
                if($i == 1000){
                    $i = 0;
                    $this->insertData(Mage::getSingleton('core/resource')->getTableName('reportsuccess/historics'),$items);
                    $items = array();
                }
                $i++;
            }
            return $this->insertData(Mage::getSingleton('core/resource')->getTableName('reportsuccess/historics'),$items);
        }catch (Exception $e){
            zend_debug::dump($e->getMessage());die;
        }
    }

    /**
     * @param $table
     * @param $data
     */
    public function insertData($table, $data){
        try{
            $connection = $this->Connection;
            $connection->beginTransaction();
            $connection->insertMultiple($table,$data);
            $connection->commit();
        }catch (Exception $e){
            $connection->rollBack();
            zend_debug::dump($e->getMessage());die;
        }

    }

    /**
     * update data to view on csv file
     */
    public function prepareDataByCsv(){
        // chưa xử lý delêt file theo 7 hay 30 hay 3 tháng.
        /*  create folder to saving the report.csv if not exist */
        if (!file_exists($this->targetDir)) {
            $this->mkDirectory($this->targetDir, 0777 , true);
        }
        $connection = $this->Connection;

        /*  delete data of this day! if exist to renew data */
        $name = 'inventoryreport-'.date("Y-m-d", Mage::getModel('core/date')->timestamp(time())).'.csv';
        if(file_exists($this->targetDir.'/'.$name))
            $this->deletefiles($name);

        $csv = "Product Id, Qty in warehouse, Warehouse Name , Mac , Inventory Value \n";//Column headers
        $count = 0;
        foreach ($this->warehouseIds as $key => $value){
            $select = $connection->select()->from(array('main_table' => Mage::getSingleton('core/resource')->getTableName('cataloginventory/stock_item')), array('product_id','total_qty'=>'qty','warehouse_id'=>'stock_id'))
                ->where('stock_id = ?',$key)
                ->joinLeft(array('mac' => Mage::getSingleton('core/resource')->getTableName('reportsuccess/costofgood')),
                    'main_table.product_id = mac.product_id', array(
                        'mac'=>'IFNULL(mac.mac,0)','inv_value'=>'main_table.qty*(IFNULL(mac.mac,0))'
                    )
                );
            $query = $connection->query($select);
            while ($row = $query->fetch()) {
                $count ++;
                if($count == 2000){
                    $count = 0;
                    $this->writeCsv($csv,$name);
                    $csv = '';
                }
                $csv.= $row['product_id'].','.$row['total_qty'].','.$value.','.$row['mac'].','.$row['inv_value']. "\n";
            }
            //header('Location: '.'http://localhost/magentodebug/csvfile.csv');
        }
        $this->writeCsv($csv,$name);
    }

    /**
     * @param $csv
     * @param $name
     */
    public function writeCsv($csv,$name){
        $csv_handler = fopen ($this->targetDir.'/'.$name,'a+');
        fwrite ($csv_handler,$csv);
        fclose ($csv_handler);
    }

    /**
     * @param $pathname
     * @param int $mode
     * @param bool $recursive
     * @return bool
     */
    public function mkDirectory($pathname, $mode = 0777, $recursive = false) {
        return mkdir($pathname, $mode, $recursive);
    }

    /**
     * @param $filename
     * @param $destination
     * @return bool
     */
    public function moveUploadedFile($filename, $destination) {
        return move_uploaded_file($filename, $destination);
    }

    /**
     * @param $filename
     * @return bool
     */
    public function deletefiles($filename){
        return unlink($this->targetDir.'/'.$filename);
    }

    /**
     * @param $patch
     * @return bool
     */
    public function changesChmod($patch){
        return  chmod($patch, 777);
    }

    /**
     * get all files name
     * @return array
     */
    public function getAllfilesName(){
        $this->targetDir = Mage::getBaseDir("media") . DS . "reportsuccess";
        // foreach (glob($this->targetDir.'/*.*') as $file) {
        $url =  Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        foreach (glob($this->targetDir.'/*.csv') as $file) {
            $link = $url.'media'.DS.'reportsuccess'.DS.str_replace($this->targetDir.'/','',$file);
            $this->list_file[str_replace($this->targetDir.'/','',$file)] = $link;
        }
        return $this->list_file;
    }
}