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

class Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Warehouses extends Mage_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'reportsuccess/inventoryreport/warehouse.phtml';

    /**
     * @return mixed
     */
    public function getHeaderText()
    {

        if($this->getreportHeader() == Magestore_Reportsuccess_Helper_Data::DETAILS){
            return $this->__('Inventory Report - Stock Quantity');
        }
        if($this->getreportHeader() == Magestore_Reportsuccess_Helper_Data::INCOMING_STOCK){
            return $this->__('Inventory Report - Incoming Stock');
        }
        if($this->getreportHeader() == Magestore_Reportsuccess_Helper_Data::HISTORICS){
            return $this->__('Inventory Report - Historical Inventory');
        }

        return $this->__('Inventory Report - Value of Stock on Hand');
    }

    /**
     * @return type of report
     */
    public function getreportHeader(){
        $code = $this->getRequest()->getParam('report');
        $report_header = Mage::helper('reportsuccess')->base64Decode($code);
        return $report_header;
    }
    /**
     * @return array
     */
    public function getOptionWarehouses(){
        $helper = Mage::helper('reportsuccess')->inventoryInstalled();
        if($helper) {
            $collection = Mage::getModel('inventorysuccess/warehouse')->getCollection();
            $collection = Magestore_Coresuccess_Model_Service::permissionService()->filterPermission(
                $collection, 'admin/reportsuccess/report/inventoryreport'
            );
            return $collection->getItems();
        }
        return false;
    }
}