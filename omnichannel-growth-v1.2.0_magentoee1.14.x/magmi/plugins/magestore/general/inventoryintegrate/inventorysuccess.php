<?php
/**
 * Created by PhpStorm.
 * User: duongdiep
 * Date: 9/7/2560
 * Time: 12:43 น.
 */
require_once("magmi_csvreader.php");
require_once("fshelper.php");

class Magmi_ReStockMagestoreInventory extends Magmi_GeneralImportPlugin {

    protected $_allWarehouseInCSV;
    protected $_rootWarehouse;
    /**
     * @return Plugin information
     */
    public function getPluginInfo() {
        return array("name" => "Update stock in Magestore InventorySuccess", "author" => " Kai ", "version" => "1.0",
            "url" => "https://www.youtube.com/watch?v=7v6A5HOjKSw");
    }

    /* protected function beforeImport()
      {
      $csvFileImported = $this->getCsvImportedFile();
      $this->log("$csvFileImported...", "info");
      return true;
      } */

    /**
     * @return bool
     */
    public function afterImport() {
        $csvObject = $this->prepareCSV();
        $csvObjectCopy = $this->prepareCSV();
        $this->_allWarehouseInCSV = $this->getAllWarehousesInCsv($csvObjectCopy);
        $this->processCsvRows($csvObject);

        $this->log(" - Import was processed!", "info");

        return true;
    }

    /**
     * @return Magmi_CSVReader
     * @throws Magmi_CSVException
     */
    private function prepareCSV()
    {
        $defaultParams = $this->getParams();
        $csvParams = $this->getCsvImportedParams();
        $params = array_merge($defaultParams, $csvParams);
        $csvreader = new Magmi_CSVReader();
        $csvreader->initialize($params, 'CSV');
        $csvreader->checkCSV();
        $csvreader->openCSV();
        $csvreader->getColumnNames();
        return $csvreader;
    }

    /**
     * @return array
     */
    protected function getCsvImportedParams() {
        $eng = $this->_callers[0];
        $ds = $eng->getPluginInstanceByClassName("datasources", "Magmi_CSVDataSource");
        if ($ds != null) {
            return $ds->getParams();
        }
        return array();
    }

    /**
     * @param $csvObject
     * @return array|void
     */
    protected function getAllWarehousesInCsv($csvObject){
        $warehouseIds = $this->getAllInventoryWarehouses();
        $return = array();
        $item = $csvObject->getNextRecord();
        if(!isset($item['sku']))return;
        foreach ($warehouseIds as $key => $w){
            foreach ($item as $row => $rvalue){
                if(isset($item['warehouse_'.$key])){
                    $return[(int)$key] = $w;
                }
            }
        }
        return $return;
    }
    public function resultGetApi($service_url){
        $ch = curl_init($service_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer"));
        $result = curl_exec($ch);
        $array = json_decode($result);
        return $array;

    }

    public function resultPutApi($service_url,$post_data){
        $ch = curl_init($service_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer"));
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($post_data));
        $result = curl_exec($ch);
    }


    /**
     * @return mixed
     */
    protected function getAllInventoryWarehouses(){
        $service_url = $this->returnUrl();
        $service_url = str_replace('api/rest/inventory/warehouseStocksMagmi', 'api/rest/inventory/warehouses' ,$service_url);
        $array = $this->resultGetApi($service_url);
        $data = get_object_vars($array);
        $warehouses = $data['items'];
        $results = array();
        $i = 0;
        foreach($warehouses as $warehouse){
            $warehouse = get_object_vars($warehouse);
            $results[$warehouse['warehouse_id']] = $warehouse['warehouse_code'];
            if($i == 0) {
                $this->_rootWarehouse = array('warehouse_id' => $warehouse['warehouse_id'],
                    'warehouse_code' => $warehouse['warehouse_code']);
            }
            $i++;
        }
        return $results;
    }

    /**
     * @param $warehouseId
     * @param $sku
     * @return mixed
     */
    protected function getWarehouseProdData($warehouseId,$sku){
        $service_url = $this->returnUrl(); //'http://127.0.0.1/magento193/api/rest/inventory/warehouseStocks/warehouse/2/productSku/test';
        $service_url = str_replace('api/rest/inventory/warehouseStocksMagmi', 'api/rest/inventory/warehouseStocks/warehouse/'.$warehouseId.'/productSku/'.$sku ,$service_url);
        $array = $this->resultGetApi($service_url);
        $data = get_object_vars($array);
        return $total_qty = $data['total_qty'];
    }

    /**
     * @return mixed
     */
    protected function returnUrl(){
        $s =  $_SERVER;
        $use_forwarded_host = false;
        $ssl      = ( ! empty( $s['HTTPS'] ) && $s['HTTPS'] == 'on' );
        $sp       = strtolower( $s['SERVER_PROTOCOL'] );
        $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
        $port     = $s['SERVER_PORT'];
        $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
        $host     = ( $use_forwarded_host && isset( $s['HTTP_X_FORWARDED_HOST'] ) ) ? $s['HTTP_X_FORWARDED_HOST'] : ( isset( $s['HTTP_HOST'] ) ? $s['HTTP_HOST'] : null );
        $host     = isset( $host ) ? $host : $s['SERVER_NAME'] . $port;
        $baseUrl = $protocol . '://' . $host;
        $absolute_url = $baseUrl. $s['REQUEST_URI'];
        $sampleUrl = str_replace('magmi/web/magmi_run.php', 'api/rest/inventory/warehouseStocksMagmi' ,$absolute_url);
        return $sampleUrl;
    }

    /**
     * @param $post_data
     */
    protected function updateStockInWarehouse($post_data){
        if(!count($post_data)){
            return ;
        }
        $service_url = $this->returnUrl();
        $this->resultPutApi($service_url,$post_data);
        $this->log(" - update Stock In Warehouse was processing!", "info");
    }

    protected function updateSupplier($post_data){
        if(!count($post_data)){
            return ;
        }
        $service_url = $this->returnUrl();
        $service_url = str_replace('api/rest/inventory/warehouseStocksMagmi', 'api/rest/inventory/supplierStocks' ,$service_url);
        $this->resultPutApi($service_url,$post_data);
        $this->log(" - update Supplier was processing!", "info");
    }

    /**
     * @param $productId
     */
    protected function updateStockItem($productId){
        if(!count($productId)){
            return ;
        }
        $table = $this->tablename('cataloginventory_stock_item');
        $sqls = '';
        foreach($productId as $id){
            if($id == null){continue;}
            $sql = 'SELECT SUM(qty) as total_avail_qty from ' . $table . ' WHERE product_id = '.$id.' AND stock_id != 1' ;
            $results = $this->selectAll($sql);
            $qty = $results[0]['total_avail_qty'];
            $sqls .= "UPDATE $table SET qty=$qty WHERE product_id=$id AND stock_id = 1; ";
        }
        $this->update($sqls);
        $this->log(" - update Stock Items was processing!", "info");
        return;
    }

    /**
     * @param $sku
     * @return mixed
     */
    protected function getProductIdFromSku($sku){
        $t = $this->tablename('catalog_product_entity');
        $result = $this->selectone("SELECT entity_id FROM $t WHERE sku='".$sku."'", null,'entity_id');
        return $result;
    }

    /**
     * @param $product_id
     * @param $warehouseId
     * @return bool
     */
    protected function productIsExistInWarehouse($product_id,$warehouseId){
        $table = $this->tablename('cataloginventory_stock_item');
        $firstSql = 'SELECT * from ' . $table . ' WHERE stock_id = '.$warehouseId.' AND product_id = '.$product_id;
        $result = $this->selectone($firstSql, null,'item_id');
        if(count($result))return true;
        else return false;
    }

    /**
     * @param $productId
     * @return mixed
     */
    protected function getTotalAvailQty($productId){
        $tableOne = $this->tablename('cataloginventory_stock_item');
        $sql = 'SELECT SUM(qty) as total_avail_qty from ' . $tableOne . ' WHERE product_id = '.$productId.' AND stock_id != 1' ;
        $results = $this->selectAll($sql);
        return $results[0]['total_avail_qty'];
    }



    /**
     * @return array
     */
    public function isRunnable()
    {
        return array(FSHelper::getExecMode() != null,"");
    }

    /**
     * @param $csvObject
     */
    protected function processCsvRows($csvObject) {
        $productIds = array();
        $post_data = array();
        $supplier_post_data = array();
        $i = 0;
        $warehouseIds = $this->_allWarehouseInCSV;
        if (!count($warehouseIds)) {  //There is not column like warehouse_1,warehouse_2,... in the csv file
            /* There is no warehouse assigned in csv file */
            $warehouse = $this->_rootWarehouse;
            $warehouseId = $warehouse['warehouse_id'];
            $warehouseCode = $warehouse['warehouse_code'];
            while (($item = $csvObject->getNextRecord()) !== false) {   // Read csv file row by row
                if (!isset($item['sku']) || ($item['sku'] == null) )
                    continue;
                $sku = $item['sku'];
                $qty = $item['qty'];
                $productId = $this->getProductIdFromSku($sku);

                    if ($this->productIsExistInWarehouse($productId, $warehouseId)) {  // Product exist in warehouse
                        $totalAvailQty = $this->getTotalAvailQty($productId);
                        $oldTotalQty = $this->getWarehouseProdData($warehouseId, $sku);
                        $updateQty = $qty - $totalAvailQty + $oldTotalQty;
                    } else {
                        $updateQty = $qty;
                    }
                    $data = array("product_sku" => $sku,
                        "warehouse_code" => $warehouseCode,
                        "operator" => "update",
                        "qty" => $updateQty);
                    $post_data[] = $data;
                    $productIds[] = $productId;

                $supplier_data = array();
                if( isset($item['supplier_id']) && ($item['supplier_id'])!= null ){
                    $supplier_data[$productId] = array('product_supplier_sku' => ( isset($item['supplier_sku']) && ($item['supplier_sku'])!= null ) ? $item['supplier_sku'] : $sku,
                        'cost' => isset($item['supplier_cost']) ? $item['supplier_cost'] : 0,
                        'tax' =>  isset($item['supplier_tax']) ? $item['supplier_tax'] : 0
                    );
                    $supplier_data['supplier_id']= $item['supplier_id'];
                    $supplier_post_data[] = $supplier_data;
                }
                $i++;
                if($i >= 100){
                    $this->updateStockInWarehouse($post_data);
                    $this->updateStockItem($productIds);
                    $this->updateSupplier($supplier_post_data);
                    $post_data = array();
                    $productIds = array();
                    $supplier_post_data = array();
                    $i = 0;
                }

            }
        } else {
            while (($item = $csvObject->getNextRecord()) !== false) {   // Read csv file row by row
                if (!isset($item['sku']))
                    continue;
                $sku = $item['sku'];
                $productId = $this->getProductIdFromSku($sku);
                foreach ($warehouseIds as $warehouseId => $warehouseCode) {
                    $newAvailQty = $item['warehouse_' . $warehouseId] ? $item['warehouse_' . $warehouseId] : 0;
                    $data = array("product_sku" => $sku,
                        "warehouse_code" => $warehouseCode,
                        "operator" => "update",
                        "qty" => $newAvailQty);
                    $post_data[] = $data;
                    $i++;

                }
                $productIds[] = $productId;

                $supplier_data = array();
                if( isset($item['supplier_id']) && ($item['supplier_id'])!= null ){
                    $supplier_data[$productId] = array('product_supplier_sku' => (isset($item['supplier_sku']) && ($item['supplier_sku'])!= null ) ? $item['supplier_sku'] : $item['sku'],
                        'cost' => isset($item['supplier_cost']) ? $item['supplier_cost'] : 0,
                        'tax' =>  isset($item['supplier_tax']) ? $item['supplier_tax'] : 0
                    );
                    $supplier_data['supplier_id']= $item['supplier_id'];
                    $supplier_post_data[] = $supplier_data;
                }

                if($i >= 100){
                    $this->updateStockInWarehouse($post_data);
                    $this->updateStockItem($productIds);
                    $this->updateSupplier($supplier_post_data);
                    $post_data = array();
                    $productIds = array();
                    $supplier_post_data = array();
                    $i = 0;
                }
            }
        }

        if(count($post_data)){
            $this->updateStockInWarehouse($post_data);
            $this->updateStockItem($productIds) ;
        }
        if(count($supplier_post_data)){
            $this->updateSupplier($supplier_post_data);
        }
        return;
    }
}