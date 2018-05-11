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
use Magestore_Reportsuccess_Helper_Variable as Variable;
use Magestore_Reportsuccess_Helper_Data as Data;
class Magestore_Reportsuccess_Model_Mysql4_Editcolumns_Collection extends
    Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('reportsuccess/editcolumns');
    }

    /**
     * @param null $typeReport
     * @param null $action
     * @return bool
     */
    public function updateDementionAndMetrics($typeReport = null,$action = null){
       if($typeReport){
           /* save Dimensions */
           $dimenstionToSave = Mage::helper('reportsuccess/variable')->_dimension($typeReport);
           $removecolumnDimensions = Mage::getModel('reportsuccess/editcolumns')->getCollection()
               ->addFieldToFilter('grid', Data::salesreportGridJsObjectdimentions)
               ->getFirstItem();
           if ($removecolumnDimensions->getId() && $dimenstionToSave){
               $removecolumnDimensions->setValue($dimenstionToSave)->save();
           }else{
               Mage::getModel('reportsuccess/editcolumns')->setGrid(Data::salesreportGridJsObjectdimentions)
                   ->setValue($dimenstionToSave)->save();
           }
           /* check and update metrics if not exist */
           $metric = Mage::getModel('reportsuccess/editcolumns')->getCollection()
               ->addFieldToFilter('grid', Data::salesreportGridJsObject)
               ->getFirstItem();
           if($metric->getId()){
               if($typeReport == Data::SALESREPORT_PRODUCT ){
                   $metric->setValue(
                       Mage::helper('reportsuccess/variable')->_metrics(Variable::_METRICS_SKU)
                   )->save();
               }
               elseif($typeReport == Data::SALESREPORT_CUSTOMER ){
                   $metric->setValue(
                       Mage::helper('reportsuccess/variable')->_metrics(Variable::_METRICS_CUSTOMER)
                   )->save();
               }
               elseif($typeReport == Data::SALESREPORT_ORDER){
                   $metric->setValue(
                       Mage::helper('reportsuccess/variable')->_metrics(Variable::_METRICS_STATUS)
                   )->save();
               }
               elseif($typeReport == Data::SALESREPORT_SHIPPING){
                   $metric->setValue(
                       Mage::helper('reportsuccess/variable')->_metrics(Variable::_METRICS_SHIPPING)
                   )->save();
               }
               elseif($typeReport == Data::SALESREPORT_PAYMENT){
                   $metric->setValue(
                       Mage::helper('reportsuccess/variable')->_metrics(Variable::_METRICS_PAYMENT)
                   )->save();
               }
               else{
                   $metric->setValue(
                       Mage::helper('reportsuccess/variable')->_metrics()
                   )->save();
               }
           }else{
               Mage::getModel('reportsuccess/editcolumns')->setGrid(Data::salesreportGridJsObject)
                   ->setValue(
                       Mage::helper('reportsuccess/variable')->_metrics()
                   )->save();
           }
       }
        if($action == Data::detailsGridJsObject){
            /* check and update metrics if not exist */
            $metric = Mage::getModel('reportsuccess/editcolumns')->getCollection()
                ->addFieldToFilter('grid', Data::detailsGridJsObject)
                ->getFirstItem();
            if(!$metric->getId()){
                Mage::getModel('reportsuccess/editcolumns')->setGrid(Data::detailsGridJsObject)
                    ->setValue(Mage::helper('reportsuccess/variable')->inventoryReportMetrics(Data::DETAILS))->save();
            }else{
                $metric->setValue(
                    Mage::helper('reportsuccess/variable')->inventoryReportMetrics(Data::DETAILS)
                )->save();
            }
        }
        if($action == Data::stockonhandGridJsObject){
            /* check and update metrics if not exist */
            $metric = Mage::getModel('reportsuccess/editcolumns')->getCollection()
                ->addFieldToFilter('grid', Data::stockonhandGridJsObject)
                ->getFirstItem();
            if(!$metric->getId()){
                Mage::getModel('reportsuccess/editcolumns')->setGrid(Data::stockonhandGridJsObject)
                    ->setValue(Mage::helper('reportsuccess/variable')->inventoryReportMetrics(Data::STOCK_ON_HAND))->save();
            }else{
                $metric->setValue(
                    Mage::helper('reportsuccess/variable')->inventoryReportMetrics(Data::STOCK_ON_HAND)
                )->save();
            }
        }

        return true;
    }
}