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
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Reportsuccess Helper
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
class Magestore_Reportsuccess_Helper_Data extends
    Mage_Core_Helper_Abstract
{
    /**
     * variables
     */
    const ALL_WAREHOUSE = 9999;
    const STOCK_ON_HAND = 'stock_on_hand';
    const DETAILS = 'details';
    const LOCATIONS = 'locations';
    const INCOMING_STOCK = 'incomingstock';
    const HISTORICS = 'historics';

    /**
     * variables
     */
    const SALESREPORT = 'sales_report';
    const SALESREPORT_PRODUCT = 'product';
    const SALESREPORT_WAREHOUSE = 'warehouse';
    const SALESREPORT_PAYMENT = 'payment_method';
    const SALESREPORT_SHIPPING = 'shipping_method';
    const SALESREPORT_ORDER = 'order_status';
    const SALESREPORT_CUSTOMER = 'customer';

    /**
     * Grid object names
     */
    const salesreportGridJsObject = 'salesreportGridJsObject';
    const salesreportGridJsObjectdimentions = 'salesreportGridJsObjectdimentions';
    const stockonhandGridJsObject = 'stockonhandGridJsObject';
    const detailsGridJsObject = 'detailsGridJsObject';


    /**
     * Get reports by type
     * @return boolean
     */
    public function inventoryInstalled()
    {
        /** @var Mage_Core_Helper_Abstract $coreHelper */
        $coreHelper = Mage::helper('core');
        if ($coreHelper->isModuleEnabled('Magestore_Inventorysuccess')
            && $coreHelper->isModuleOutputEnabled('Magestore_Inventorysuccess')
        ) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function purchaseInstalled(){
        $coreHelper = Mage::helper('core');
        if ($coreHelper->isModuleEnabled('Magestore_Purchaseordersuccess')
            && $coreHelper->isModuleOutputEnabled('Magestore_Purchaseordersuccess')
        ) {
            return true;
        }
        return false;
    }

    /**
     *
     * @param string $data
     * @return string
     */
    public function base64Decode($data, $strict = false)
    {
        return base64_decode($data, $strict);
    }

    /**
     *
     * @param string $data
     * @return string
     */
    public function base64Encode($data)
    {
        return base64_encode($data);
    }

    /**
     * Get reports by type
     *
     * @param string $type
     * @return array
     */
    public function getReports($type = null)
    {
        $reports = array(
            'sales' => array(
                'title' => $this->__('Sales Reports'),
                'description' => $this->__(' View your Sale Reports, COGS, Profit... '),
                'reports' => array(
                    'product' => array(
                        'code' => 'product',
                        'title' => $this->__('Product'),
                        'description' => $this->__('Total sales by Product'),
                        'sort' => 1,
                    ),
                    'warehouse' => array(
                        'code' => 'warehouse',
                        'title' => $this->__('Warehouse'),
                        'description' => $this->__('Total sales by Warehouse'),
                        'sort' => 1,
                    ),
                    'shipping_method' => array(
                        'code' => 'shipping_method',
                        'title' => $this->__('Shipping method'),
                        'description' => $this->__('Total sales by Shipping method'),
                        'sort' => 1,
                    ),
                    'payment_method' => array(
                        'code' => 'payment_method',
                        'title' => $this->__('Payment method'),
                        'description' => $this->__('Total sales by Payment method'),
                        'sort' => 1,
                    ),
                    'order_status' => array(
                        'code' => 'order_status',
                        'title' => $this->__('Order status'),
                        'description' => $this->__('Total sales by Order status'),
                        'sort' => 1,
                    ),
                    'customer' => array(
                        'code' => 'customer',
                        'title' => $this->__('Customer'),
                        'description' => $this->__('Total sales by Customer'),
                        'sort' => 1,
                    ),
                ),
            ),
            'stockonhand' => array(
                'title' => $this->__('Inventory Reports'),
                'description' => $this->__('View your current stock...'),
                'reports' => array(
                    'stock_on_hand' => array(
                        'code' => 'stockonhand',
                        'title' => $this->__('Value of Stock on Hand'),
                        'description' => $this->__('View your current stock levels and stock values on hand'),
                        'sort' => 1,
                    ),
                    'details' => array(
                        'code' => 'details',
                        'title' => $this->__('Stock Quantity'),
                        'description' => $this->__('View your stock levels on hand, commited, available and on order'),
                        'sort' => 1,
                    ),
                    'locations' => array(
                        'code' => 'locations',
                        'title' => $this->__('Compare by Warehouse'),
                        'description' => $this->__('Compare your stock levels in multiple locations'),
                        'sort' => 1,
                    ),
                    'incoming_stock' => array(
                        'code' => 'incomingstock',
                        'title' => $this->__('Incoming Stock'),
                        'description' => $this->__('View a list of variants that will come in from the purchase orders'),
                        'sort' => 1,
                    ),
                    'historics_report' => array(
                        'code' => 'historics',
                        'title' => $this->__('Historical Inventory'),
                        'description' => $this->__('Export your stock on hand and MAC at any point in history'),
                        'sort' => 1,
                    ),
                ),
            ),
        );

        /* remove if not install PurchaseOrder */
        if(!$this->purchaseInstalled()){
            unset($reports['stockonhand']['reports']['incoming_stock']);
        }
        return $reports;
    }
    public function prepareDataNotPurchaseOrder($data,$type){
        if(!$this->purchaseInstalled()){
            if($type == Magestore_Reportsuccess_Helper_Data::DETAILS){
                $data->removeColumn('supplier_name');
                $data->removeColumn('qty_in_order');
                return $data;
            }
        }
    }
    /**
     * @return Magestore_Reportsuccess_Model_Service_Inventoryreport_InventoryService
     */
    public function service()
    {
        return Magestore_Coresuccess_Model_Service::reportInventoryService();
    }

    /**
     * @param $type
     * @return string|void
     */
    public function totalService($type){
        return Magestore_Reportsuccess_Model_Service_Totalreport_TotalsCollection::getTotals($type);
    }

}