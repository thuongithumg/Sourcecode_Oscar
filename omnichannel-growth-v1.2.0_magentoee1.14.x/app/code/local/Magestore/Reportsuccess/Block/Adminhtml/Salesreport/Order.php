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

use Magestore_Reportsuccess_Helper_Data as Data;
class Magestore_Reportsuccess_Block_Adminhtml_Salesreport_Order
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var array
     */
    protected $MAPPING_FIELD = array(
        'order_id' => 'main_table.order_id',
        'status' => 'main_table.status',
        'order_potential_sold_qty' => 'SUM(main_table.potential_sold_qty)',
        'order_realized_sold_qty' => 'SUM(main_table.realized_sold_qty)',
        'order_cogs' => 'SUM(main_table.cogs)',
        'order_profit' => 'SUM(main_table.profit)',
        'order_tax' => 'SUM(main_table.tax)',
        'order_discount' => 'SUM(main_table.realized_discount + main_table.potential_discount)',
        'order_total_sale' => 'SUM(main_table.total_sale)',
    );

    /**
     * contruct
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('salesreportOrderGrid');
        $this->setDefaultSort('order_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection for grid product
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->getDataColllection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Get Collection for grid product
     *
     * @return Collection
     */
    public function getDataColllection()
    {
        $orderIds = $this->OrderedIdsSession();
        $warehouseIds = $this->WarehouseSession(); //$this->getRequest()->getParam('warehouse_ids', 0);
        /** @var Magestore_Reportsuccess_Model_Mysql4_Salesreport_Collection $collection */
        $collection = Mage::getResourceModel('reportsuccess/salesreport_order_collection')
            ->addFieldToFilter('id', array('in' => explode(',', $orderIds)))
            ->addFieldToFilter('warehouse_id', array('in' => explode(',', $warehouseIds)));

        /* join attribute code */
        Mage::helper('reportsuccess')->service()->attributeMapping($collection);

        $collection->getSelect()
            ->columns(array(
                'order_potential_sold_qty' => new Zend_Db_Expr($this->MAPPING_FIELD['order_potential_sold_qty']),
                'order_realized_sold_qty' => new Zend_Db_Expr($this->MAPPING_FIELD['order_realized_sold_qty']),
                'order_cogs' => new Zend_Db_Expr($this->MAPPING_FIELD['order_cogs']),
                'order_profit' => new Zend_Db_Expr($this->MAPPING_FIELD['order_profit']),
                'order_tax' => new Zend_Db_Expr($this->MAPPING_FIELD['order_tax']),
                'order_discount' => new Zend_Db_Expr($this->MAPPING_FIELD['order_discount']),
                'order_total_sale' => new Zend_Db_Expr($this->MAPPING_FIELD['order_total_sale']),
            ))->group('item_id');
        $collection
            ->setOrder('id', 'DESC');
        return $collection;
    }

    /**
     * @return array
     */
    public function OrderedIdsSession(){
        $orderIds = $this->getRequest()->getParam('order_ids', 0);
        $orderIds_Session = Mage::getModel('admin/session')->getData('sales_report_order_ids_session');
        if($orderIds == 0){
            if(!$orderIds_Session){
                return array(0);
            }else{
                return $orderIds_Session;
            }
        }else{
            Mage::getModel('admin/session')->setData('sales_report_order_ids_session',$orderIds);
            return $orderIds;
        }
    }

    /**
     * @return mixed
     */
    public function WarehouseSession(){
        /* return Magestore_Reportsuccess_Model_Service_Inventoryreport_Modifigrids_Modifigrids locationWarehouse() */
        return  Mage::getModel('admin/session')->getData('warehouse_locations');
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('sku',
            array(
                'header' => $this->__('SKU'),
                'align' => 'left',
                'index' => 'sku',
            ))
            ->addColumn('increment_id',
            array(
                'header' => $this->__('Order ID'),
                'align' => 'left',
                'index' => 'increment_id',
            ))
            ->addColumn('item_id',
                array(
                    'header' => $this->__('Item ID'),
                    'align' => 'left',
                    'index' => 'item_id',
                ))
            ->addColumn('status',
                array(
                    'header' => $this->__('Status'),
                    'align' => 'left',
                    'index' => 'status',
                ))->addColumn('order_realized_sold_qty',
                array(
                    'header' => $this->__('Actual Sold Qty'),
                    'align' => 'left',
                    'index' => 'order_realized_sold_qty',
                    'type' => 'number',
                    'filter_condition_callback' => array($this, '_filterCallback')
                ))->addColumn('order_potential_sold_qty',
                array(
                    'header' => $this->__('Potential Sold Qty'),
                    'align' => 'left',
                    'index' => 'order_potential_sold_qty',
                    'type' => 'number',
                    'filter_condition_callback' => array($this, '_filterCallback')
                ))->addColumn('order_cogs',
                array(
                    'header' => $this->__('COGS'),
                    'align' => 'left',
                    'index' => 'order_cogs',
                    'type' => 'number',
                    'filter_condition_callback' => array($this, '_filterCallback')
                ))->addColumn('order_profit',
                array(
                    'header' => $this->__('Profit'),
                    'align' => 'left',
                    'index' => 'order_profit',
                    'type' => 'currency',
                    'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
                    'filter_condition_callback' => array($this, '_filterCallback')
                ))->addColumn('order_tax',
                array(
                    'header' => $this->__('Tax'),
                    'align' => 'left',
                    'index' => 'order_tax',
                    'type' => 'currency',
                    'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
                    'filter_condition_callback' => array($this, '_filterCallback')
                ))->addColumn('order_discount',
                array(
                    'header' => $this->__('Discount'),
                    'align' => 'left',
                    'index' => 'order_discount',
                    'type' => 'currency',
                    'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
                    'filter_condition_callback' => array($this, '_filterCallback')
                ))->addColumn('order_total_sale',
                array(
                    'header' => $this->__('Total Sales'),
                    'align' => 'left',
                    'index' => 'order_total_sale',
                    'type' => 'currency',
                    'currency_code' => Mage::app()->getStore()->getBaseCurrencyCode(),
                    'filter_condition_callback' => array($this, '_filterCallback')
                ));

        $this->addExportType('*/*/exportCsv', $this->__('CSV'));
        $this->addExportType('*/*/exportXml', $this->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * @return mixed
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/salesreport_index/order', array(
            '_current' => true
        ));
    }


    /**
     * Apply `qty` filter to product grid.
     *
     * @param Magestore_Reportsuccess_Model_Mysql4_Salesreport_Collection $collection
     * @param $column
     */
    protected function _filterCallback($collection, $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        if (!is_array($value))
            return $this;

        if (isset($value['from']))
            $collection->getSelect()->having($this->MAPPING_FIELD[$column->getId()] . ' >= ?', $value['from']);
        if (isset($value['to']))
            $collection->getSelect()->having($this->MAPPING_FIELD[$column->getId()] . ' <= ?', $value['to']);
    }
}