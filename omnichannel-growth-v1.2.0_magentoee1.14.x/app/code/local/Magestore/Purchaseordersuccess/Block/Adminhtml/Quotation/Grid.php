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
class Magestore_Purchaseordersuccess_Block_Adminhtml_Quotation_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('quotation_list');
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
        $collection = Mage::getResourceModel('purchaseordersuccess/purchaseorder_collection')
            ->getAllQuotation();
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
        $this->addColumn('purchase_code',
            array(
                'header'    => $this->__('Quotation Number'),
                'align'     =>'right',
                'width'     => '50px',
                'index'     => 'purchase_code',
        ))->addColumn('purchased_at',
            array(
                'header'    => $this->__('Create At'),
                'align'     =>'left',
                'index'     => 'purchased_at',
                'type'      => 'date',
                'filter_condition_callback' => array($this, '_filterDateCallback')
        ))->addColumn('supplier_id',
            array(
                'header'    => $this->__('Supplier'),
                'align'     =>'left',
                'index'     => 'supplier_id',
                'type'      => 'options',
                'options'   => Mage::getSingleton('purchaseordersuccess/purchaseorder_options_supplier')->getOptionHash()
        ))->addColumn('total_qty_orderred',
            array(
                'header'    => $this->__('Ordered Qty'),
                'align'     =>'left',
                'index'     => 'total_qty_orderred',
                'type'      => 'number',
        ))->addColumn('grand_total_incl_tax',
            array(
                'header'    => $this->__('Grand Total (Incl. Tax)'),
                'align'     =>'left',
                'index'     => 'grand_total_incl_tax',
                'width'     => '200px',
                'type'      => 'number',
        ))->addColumn('status',
            array(
                'header'    => $this->__('Status'),
                'align'     =>'left',
                'index'     => 'status',
                'type'      => 'options',
                'options'   => Mage::getSingleton('purchaseordersuccess/purchaseorder_options_status')->getOptionHash()
        ));
        if(!$this->_isExport)
            $this->addColumn('action',
                array(
                    'header'    =>    $this->__('Action'),
                    'width'     => '100',
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
     * @param $collection
     * @param $column
     */
    protected function _filterTotalQtyCallback($collection, $column) {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        $collection->addQtyToFilter($column->getId(), $value);
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