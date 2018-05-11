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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseordersuccess Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Grid
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Abstractgrid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('return_list');
        $this->setDefaultSort('purchase_code');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare collection for block to display
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('purchaseordersuccess/return_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('return_code',
            array(
                'header'    => $this->__('Return Request Number'),
                'align'     => 'right',
                'width'     => '200',
                'index'     => 'return_code',
            ))->addColumn('returned_at',
            array(
                'header'    => $this->__('Return At'),
                'align'     =>'left',
                'index'     => 'returned_at',
                'type'      => 'date',
                'filter_condition_callback' => array($this, '_filterDateCallback')
            ))->addColumn('supplier_id',
            array(
                'header'    => $this->__('Supplier'),
                'align'     =>'left',
                'index'     => 'supplier_id',
                'type'      => 'options',
                'options'   => Mage::getSingleton('purchaseordersuccess/purchaseorder_options_supplier')->getOptionHash()
            ))->addColumn('warehouse_id',
            array(
                'header'    => $this->__('Warehouse'),
                'align'     =>'left',
                'index'     => 'warehouse_id',
                'type'      => 'options',
                'options'   => Mage::getSingleton('purchaseordersuccess/return_options_warehouse')->getOptionHash()
            ))->addColumn('total_qty_returned',
            array(
                'header'    => $this->__('Returned Qty'),
                'align'     =>'left',
                'index'     => 'total_qty_returned',
                'type'      => 'number',
            ))->addColumn('total_qty_transferred',
            array(
                'header'    => $this->__('Delivered Qty'),
                'align'     =>'left',
                'index'     => 'total_qty_transferred',
                'type'      => 'number',
            ))->addColumn('status',
            array(
                'header'    => $this->__('Status'),
                'align'     =>'left',
                'index'     => 'status',
                'type'      => 'options',
                'options'   => Mage::getSingleton('purchaseordersuccess/return_options_status')->getOptionHash()
            ));
        if(!$this->_isExport)
            $this->addColumn('action',
                array(
                    'header'    =>    $this->__('Action'),
                    'type'      => 'action',
                    'getter'    => 'getId',
                    'actions'   => array(
                        array(
                            'caption'    => $this->__('View'),
                            'url'        => array('base'=> '*/*/view'),
                            'field'      => 'id'
                        )),
                    'filter'    => false,
                    'sortable'    => false,
                ));

        $this->addExportType('*/*/exportCsv', $this->__('CSV'));
        $this->addExportType('*/*/exportXml', $this->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array('id' => $row->getId()));
    }

    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * Apply `qty` filter to product grid.
     *
     * @param $collection
     * @param $column
     */
    protected function _filterDateCallback($collection, $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        if ($column->getType() == 'date') {
            if (isset($value['from'])) {
                $collection->addFieldToFilter(
                    $column->getIndex(),
                    array(
                        'gteq' => $value['from']->set(
                                $value['orig_from'], Zend_Date::DATE_SHORT, $value['locale']
                            )->toString('Y-M-d') . ' 00:00:00'
                    )
                );
            }
            if (isset($value['to'])) {
                $collection->addFieldToFilter(
                    $column->getIndex(),
                    array(
                        'lteq' => $value['to']->set(
                                $value['orig_to'], Zend_Date::DATE_SHORT, $value['locale']
                            )->toString('Y-M-d') . ' 23:59:59'
                    )
                );
            }
        }
        return $collection;
    }
}