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
 * ReportSuccess Resource Model
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */

use Magestore_Reportsuccess_Helper_Data as Data;
use Magestore_Reportsuccess_Model_Service_Mapping_Totalreport as Mapping;
class Magestore_Reportsuccess_Model_Service_Totalreport_TotalsCollection
{
    /**
     * @param $type
     * @return string|void
     */
    static function getTotals($type)
    {
        $cr = Mage::getSingleton('core/resource');
        $collection = self::getCollection();
        if($type == Data::SALESREPORT){
            $warehouseIds_Session = self::getWarehouseSession();
            $date = self::getDateSession();
            $collection = "select * from {$cr->getTableName('reportsuccess/salesreport')} where 1 ";
            if($warehouseIds_Session && $warehouseIds_Session != Data::ALL_WAREHOUSE){
                $collection .= " and warehouse_id in ({$warehouseIds_Session})";
            }else{
                $warehouseIds_Session = Data::ALL_WAREHOUSE;
                $collection .= " and warehouse_id in ({$warehouseIds_Session})";
            }
            if($date){
                $from = date("Y-m-d", strtotime($date['date_from'])) . ' 00:00:00';
                $to = date("Y-m-d", strtotime($date['date_to'])) . ' 23:59:59';
                $collection .= "  and created_at >= '".$from."' and  created_at <= '".$to."' ";
            }
        }
        self::createTempTable(Mapping::TEMP_TABLE,$collection);

        if($type == Mapping::ON_HAND)
            $check= "SELECT ".Mapping::FIELD_ON_HAND." FROM ".$cr->getTableName(Mapping::TEMP_TABLE)." ";

        if($type == Mapping::DETAILS)
            $check= "SELECT ".Mapping::FIELD_DETAILS." FROM ".$cr->getTableName(Mapping::TEMP_TABLE)." ";

        if($type == Mapping::INCOMING_STOCK)
            $check= "SELECT ".Mapping::FIELD_INCOMING." FROM ".$cr->getTableName(Mapping::TEMP_TABLE)."
            LEFT JOIN `os_supplier` AS `supplier` ON inventory_report_collection.supplier_id = supplier.supplier_id
            group by inventory_report_collection.supplier_id ";

        if($type == Data::SALESREPORT){
            $check= "SELECT ".Mapping::FIELD_ON_SALES." FROM ".$cr->getTableName(Mapping::TEMP_TABLE)." ";
        }
        $resultcheck = $cr->getConnection('core_write')->fetchAll($check);

        $data = self::mappingFields($resultcheck,$type);
        if($type == Mapping::INCOMING_STOCK)
            return self::incomingreport($data);

        return self::tohtml($data);
    }

    /**
     * @return collection
     */
    protected function getCollection(){
        return Mage::getSingleton('adminhtml/session')->getData("collectiondata");
    }

    /**
     * @return warehouses
     */
    protected function getWarehouseSession(){
        return Mage::getModel('admin/session')->getData('warehouse_locations');
    }

    /**
     * @return temp date time
     */
    protected function getTempDate(){
        return Mage::getSingleton('reportsuccess/service_inventoryreport_modifigrids_modifigrids')->temp_date();
    }
    /**
     * @return date time
     */
    protected function getDateSession(){
        $date =  Mage::getModel('admin/session')->getData('report_select_date');
        if(!$date){
            $date['date_from'] = self::getTempDate();
            $date['date_to'] = self::getTempDate();
        }
        return $date;
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
     * @param $resultcheck
     * @param $type
     * @return array
     */
    protected function mappingFields($resultcheck,$type){
        $totals = '';
        if($resultcheck){
            if($type == Mapping::ON_HAND){
                //$symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
                $totals = Mapping::_mapping_field_on_hand();
                foreach ($totals as &$value) {
                    foreach ($resultcheck[0] as $key => $data) {
                        if($value === 'sum_total_qty'){
                            $value = round($data,2);
                        }elseif($value === $key){
                            $value = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->toCurrency($data);
                        }
                    }
                }
            }
            if($type == Mapping::DETAILS){
                //$symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
                $totals = Mapping::_mapping_field_details();
                foreach ($totals as &$value) {
                    foreach ($resultcheck[0] as $key => $data) {
                        if($value === $key){
                            if($data === 0){
                                $value = 0;
                            }else{
                                $value = round($data,2);
                            }
                        }
                    }
                }
            }
            if($type == Mapping::INCOMING_STOCK){
                $totals = $resultcheck;
            }

            if($type == Data::SALESREPORT){
                $totals = Mapping::_mapping_field_on_sales();
                foreach ($totals as &$value) {
                    foreach ($resultcheck[0] as $key => $data) {
                        if( ($value === 'sum_realized_sold_qty' && $value === $key) || ($value === 'sum_potential_sold_qty' && $value === $key ) ){
                            $value = round($data,2);
                        }elseif($value === $key){
                            $value = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->toCurrency($data);
                        }
                    }
                }
            }
        }
        return $totals;
    }

    /**
     * @param $data
     * @return string
     */
    static function tohtml($data){
        $html = '';
            foreach ($data as $key => $value){
                $html.= ' <div class="span3 data-well"> ';
                $html.= '<h2>'.$value.'</h2>';
                $html.= '<span class="light-text">';
                $html.= ' <span class="translation_missing" title="">'.$key.'</span>';
                $html.= ' </span>';
                $html.= ' </div>';
            }
        return $html;
    }

    public function incomingreport($data){
            $css = Mapping::CSS;
            $html_total_cost = '<div class="data-well-level1">';
            //$html_total_cost.=  '<h2>Total Cost by Supplier</h2>';
            $html_total_stock = '<div class="data-well-level1">';
            //$html_total_stock.=  '<h2>Incoming Stock by Supplier</h2>';
            if($data){
                foreach ($data as $html){
                     foreach ($html as $key=>$value){
                         if($key === 'total_cost_group'){
                             $value = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->toCurrency($value);
                             $html_total_cost .= '<div class="data-well-level2">';
                             $html_total_cost .= '<h2>'.$value.'</h2>';
                             $html_total_cost .= '<h2 class=" translation_missing_lv2" >'.$html['supplier'].'</h2>';
                             $html_total_cost .= '</div>';
                         }
                         if($key === 'total_in_coming_group'){
                             $html_total_stock .= '<div class="data-well-level2">';
                             $html_total_stock .= '<h2>'.round($value,2).'</h2>';
                             $html_total_stock .= '<h2 class=" translation_missing_lv2" >'.$html['supplier'].'</h2>';
                             $html_total_stock .= '</div>';
                         }
                     }
                }
            }

//            foreach ($data as $html){
//                $colors = self::random_color();
//                foreach ($html as $key=>$value){
//                    if($key === 'total_cost_group'){
//                        $value = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->toCurrency($value);
//                        $html_total_cost .= '<div class="data-well-level2">';
//                        $html_total_cost .= '<h2 style="color:#'.$colors.'!important; ">';
//                        $html_total_cost .= "|".$html['supplier']." ".$value;
//                        $html_total_cost .= '</h2>';
//                        $html_total_cost .= '</div>';
//                    }
//                    if($key === 'total_in_coming_group'){
//                        $html_total_stock .= '<div class="data-well-level2">';
//                        $html_total_stock .= '<h2 style="color:#'.$colors.'!important; ">';
//                        $html_total_stock .= "|".$html['supplier']." ".round($value,2);
//                        $html_total_stock .= '</h2>';
//                        $html_total_stock .= '</div>';
//                    }
//                }
//            }

            $html_total_cost .='</br>';
            $html_total_cost.= '<span class=" translation_missing" title="">Total Cost by Supplier</span>';
            $html_total_cost .='</div>';

            $html_total_stock .='</br>';
            //$html_total_stock.=  '<h2>Incoming Stock by Supplier</h2>';
            $html_total_stock.= '<span class="translation_missing" title="">Incoming Stock by Supplier</span>';
            $html_total_cost .='</div>';

            $html_total_stock .='</div>';
          return $html_total_stock.$html_total_cost.$css;
    }
    public function random_color_part() {
        return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
    }

    public function random_color() {
        return self::random_color_part() .self::random_color_part() . self::random_color_part();
    }

}