<?php
/**
 * Created by PhpStorm.
 * User: duongdiep
 * Date: 19/02/2017
 * Time: 17:08
 */

class Magestore_Debugsuccess_Model_Service_Debug_DebugService
    extends Magestore_Debugsuccess_Model_Service_Debug_Modify_Grid
{
    /**
     * Return warehouse product collection with product information and total qtys
     *
     * @return Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Product_Collection
     */
    public function getAllStockWrongQty()
    {
        return Mage::getResourceModel('debugsuccess/wrongqty_collection')->getAllStockWrongQtyCollection();
    }

    /**
     * @param $grid
     * @param $type
     */
    public function modifiColumns($grid,$type){
        return $this->Columns($grid,$type);
    }

    /**
     * @param $collection
     * @param $column
     * @param $value
     * @return mixed
     */
    public function filterDebugCallback($collection,$column,$value){
        return Mage::getResourceModel('debugsuccess/wrongqty_collection')->filterDebugCallback($collection,$column,$value);
    }

    /**
     *
     */
    public function correctWrongQty($product,$warehouse){
        return Mage::getResourceModel('debugsuccess/wrongqty')->correctWrongQty($product,$warehouse);
    }

    public function getOptionArrayForDebug(){

    }

    public static function correctService(){
        return Mage::getSingleton('debugsuccess/service_debug_correctService');
    }


}