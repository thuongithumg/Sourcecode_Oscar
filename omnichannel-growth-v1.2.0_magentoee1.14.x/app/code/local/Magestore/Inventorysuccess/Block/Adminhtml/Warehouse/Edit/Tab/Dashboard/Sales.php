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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Warehouse Edit Dashboard Sales Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Dashboard_Sales 
    extends Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Dashboard_AbstractChart
{
    /**
     * @var string
     */
    protected $_template = 'inventorysuccess/warehouse/edit/tab/dashboard/sales.phtml';

    /**
     * @var string
     */
    protected $_salesReportLast30days;

    /**
     * @var string
     */
    protected $_orderQty30days;

    /**
     * @var string
     */
    protected $_itemQty30days;

    /**
     * @var string
     */
    protected $_revenue30days;
    
    public function __construct(array $args)
    {
        parent::__construct($args);
        $this->setContainerId('sales_chart_container');
        $this->setTitle('Sales');
        $this->setSubtitle('Last 30 Days Sales Reports');
        $this->getSalesReportLast30days();
        $this->getTotalOrderItemLast30days();
    }

    public function getSalesReportLast30days(){
        if(!$this->_salesReportLast30days){
            $this->_salesReportLast30days = Mage::getResourceModel('inventorysuccess/sales_order_item_collection')
                ->getSalesReport($this->getRequest()->getParam('id'), 30);
        }
        return $this->_salesReportLast30days;
    }

    public function getTotalOrderItemLast30days(){
        $collection = $this->_salesReportLast30days;
        $collection->getTotalOrderItem();
        $data = $collection->getData();
        $totalByDay = array();
        foreach ($data as $item) {
            $totalByDay[$item['date_without_hour']] = $item;
        }
        return $this->addSalesChartData($totalByDay);
    }

    public function addSalesChartData($totalByDay){
        $this->_orderQty30days = '';
        $this->_itemQty30days = '';
        $this->_revenue30days = '';
        for ($i = 30; $i >= 0; $i--) {
            $date = Mage::getSingleton('core/date')->gmtDate('Y-m-d', strtotime('-'.$i.' days'));
            if ($i != 30){
                $this->_orderQty30days .= ', ';
                $this->_itemQty30days .= ', ';
                $this->_revenue30days .= ', ';
            }if (isset($totalByDay[$date])) {
                $this->_orderQty30days .= round($totalByDay[$date]['order_by_day'], 2);
                $this->_itemQty30days .= round($totalByDay[$date]['item_qty_by_day'], 2);
                $this->_revenue30days .= round($totalByDay[$date]['revenue_by_day'], 2);
            } else {
                $this->_orderQty30days .= '0';
                $this->_itemQty30days .= '0';
                $this->_revenue30days .= '0';
            }
        }
        return $this;
    }

    public function getOrderQty30days(){
        return $this->_orderQty30days;
    }

    public function getItemQty30days(){
        return $this->_itemQty30days;
    }

    public function getRevenue30days(){
        return $this->_revenue30days;
    }
}