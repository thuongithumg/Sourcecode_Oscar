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
 * Warehouse Edit Dashboard Tab Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Dashboard extends Mage_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'inventorysuccess/warehouse/edit/tab/dashboard.phtml';

    /**
     * @var int current warehouse id
     */
    protected $currentWarehouseId;

    /**
     * @var array all totals qty of current warehouse
     */
    protected $allTotalsQty;

    /**
     * @var array
     */
    protected $localeFormat;

    /**
     * @var Mage_Core_Block_Template[]
     */
    protected $blockGrid = array();
    

    public function __construct(array $args)
    {
        parent::__construct($args);
        $this->localeFormat = Mage::app()->getLocale()->getJsPriceFormat();
    }

    /**
     * Retrieve instance of grid block
     *
     * @param string $blockClass
     * @param string $blockName
     * @return Mage_Core_Block_Template
     * @throws Mage_Core_Block_Template
     */
    public function getChart($blockClass, $blockName)
    {
        if (!isset($this->blockGrid[$blockName])) {
            $this->blockGrid[$blockName] = $this->getLayout()->createBlock(
                $blockClass, $blockName
            );
        }
        return $this->blockGrid[$blockName];
    }

    /**
     * Return HTML of grid block
     *
     * @param string $blockClass
     * @param string $blockName
     * @return string
     */
    public function getChartHtml($blockClass, $blockName)
    {
        return $this->getChart($blockClass, $blockName)->toHtml();
    }

    /**
     * @return int|null
     *
     */
    protected function getCurrentWarehouseId()
    {
        if (!$this->currentWarehouseId)
            $this->currentWarehouseId = $this->getRequest()->getParam('id', null);
        return $this->currentWarehouseId;
    }

    /**
     * 
     * 
     * @return array
     */
    public function getAllTotalsQty()
    {
        if (!$this->allTotalsQty)
            $this->allTotalsQty = Mage::getResourceModel('inventorysuccess/warehouse_product_collection')
                ->getTotalQtysFromWarehouse($this->getCurrentWarehouseId())
                ->getData();
        return $this->allTotalsQty;
    }

    /**
     * @return decimal
     */
    public function getSumTotalQty()
    {
        $allTotalQty = $this->getAllTotalsQty();
        return isset($allTotalQty['sum_total_qty']) ?
            number_format(floatval($allTotalQty['sum_total_qty']), 0, $this->localeFormat['decimalSymbol'], $this->localeFormat['groupSymbol']) :
            0;
    }

    /**
     * @return decimal
     */
    public function getSumQtyToShip()
    {
        $allTotalQty = $this->getAllTotalsQty();
        return isset($allTotalQty['sum_qty_to_ship']) ?
            number_format(floatval($allTotalQty['sum_qty_to_ship']), 0, $this->localeFormat['decimalSymbol'], $this->localeFormat['groupSymbol']) :
            0;
    }

    /**
     * @return decimal
     */
    public function getSumAvailableQty()
    {
        $allTotalQty = $this->getAllTotalsQty();
        return isset($allTotalQty['available_qty']) ?
            number_format(floatval($allTotalQty['available_qty']), 0, $this->localeFormat['decimalSymbol'], $this->localeFormat['groupSymbol']) :
            0;
    }

    public function getSalesReport()
    {
        $chart = $this->getChart(
            'inventorysuccess/adminhtml_warehouse_edit_tab_dashboard_sales',
            'warehouse.dashboard.sales'
        );
        return $chart;
    }

    protected function getTotalSales($totalString)
    {
        $totalArray = explode(',', $totalString);
        return array_sum($totalArray);
    }

    public function getTotalOrderQty30days()
    {
        $orderQtyString = $this->getSalesReport()->getOrderQty30days();
        return number_format(
            $this->getTotalSales($orderQtyString),
            0,
            $this->localeFormat['decimalSymbol'],
            $this->localeFormat['groupSymbol']
        );
    }

    public function getTotalItemQty30days()
    {
        $itemQtyString = $this->getSalesReport()->getItemQty30days();
        return number_format(
            $this->getTotalSales($itemQtyString),
            0,
            $this->localeFormat['decimalSymbol'],
            $this->localeFormat['groupSymbol']
        );
    }

    public function getTotalRevenue30days()
    {
        $revenueString = $this->getSalesReport()->getRevenue30days();
        return Mage::helper('core')->formatPrice($this->getTotalSales($revenueString), true);
//        return Mage::getSingleton('directory/currency')->format($this->getTotalSales($revenueString));
    }
}