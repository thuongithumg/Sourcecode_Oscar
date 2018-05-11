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
 * @package     Magestore_Reportsuccess
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 *
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */

class Magestore_Reportsuccess_Block_Adminhtml_Salesreport_Criteria extends Mage_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'reportsuccess/salesreport/criteria.phtml';

    /**
     * @return mixed
     */
    public function getHeaderText()
    {
        if($this->getreportHeader() == Magestore_Reportsuccess_Helper_Data::SALESREPORT_PRODUCT){
            return $this->__('Sales Report - By Product SKU');
        }
        if($this->getreportHeader() == Magestore_Reportsuccess_Helper_Data::SALESREPORT_WAREHOUSE){
            return $this->__('Sales Report - By Warehouse');
        }
        if($this->getreportHeader() == Magestore_Reportsuccess_Helper_Data::SALESREPORT_PAYMENT){
            return $this->__('Sales Report - By Payment Method');
        }
        if($this->getreportHeader() == Magestore_Reportsuccess_Helper_Data::SALESREPORT_SHIPPING){
            return $this->__('Sales Report - By Shipping Method');
        }
        if($this->getreportHeader() == Magestore_Reportsuccess_Helper_Data::SALESREPORT_ORDER){
            return $this->__('Sales Report - By Order');
        }
        if($this->getreportHeader() == Magestore_Reportsuccess_Helper_Data::SALESREPORT_CUSTOMER){
            return $this->__('Sales Report - By Customer');
        }
        return $this->__('Sales Report');
    }

    /**
     * @return type of report
     */
    public function getreportHeader(){
        return $code = $this->getRequest()->getParam('type');
    }
    /**
     * @return array
     */
    public function getOptionWarehouses(){
        $helper = Mage::helper('reportsuccess')->inventoryInstalled();
        if($helper) {
            $collection = Mage::getModel('inventorysuccess/warehouse')->getCollection();
            $collection = Magestore_Coresuccess_Model_Service::permissionService()->filterPermission(
                $collection, 'admin/reportsuccess/report/salereport'
            );
            return $collection->getItems();
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function lastUpdateSaleReport(){
        return Mage::getSingleton('reportsuccess/service_salesreport_salesReportService')->getLastUpdateSalesReport();
    }
}