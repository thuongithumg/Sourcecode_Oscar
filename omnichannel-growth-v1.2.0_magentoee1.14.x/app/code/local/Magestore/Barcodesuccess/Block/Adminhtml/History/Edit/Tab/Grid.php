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
 * @package     Magestore_Barcodesuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Barcodesuccess Grid Block
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @author      Magestore Developer
 */
class Magestore_Barcodesuccess_Block_Adminhtml_History_Edit_Tab_Grid extends
    Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Magestore_Barcodesuccess_Block_Adminhtml_History_Edit_Tab_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('historyTabGrid');
        $this->setDefaultSort(Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID);
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Grid
     */
    protected function _prepareCollection()
    {
        $historyId  = $this->getRequest()->getParam('id');
        $collection = Mage::getModel('barcodesuccess/barcode')->getCollection()
                          ->addFieldToFilter(Magestore_Barcodesuccess_Model_Barcode::HISTORY_ID, $historyId);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID, array(
            'header' => Mage::helper('barcodesuccess')->__('ID'),
            'align'  => 'right',
            'width'  => '50px',
            'index'  => Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID,
        ));

        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::BARCODE, array(
            'header' => Mage::helper('barcodesuccess')->__('Barcode'),
            'align'  => 'left',
            'width'  => '250px',
            'index'  => Magestore_Barcodesuccess_Model_Barcode::BARCODE,
        ));

        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::PRODUCT_SKU, array(
            'header' => Mage::helper('barcodesuccess')->__('SKU'),
            'width'  => '250px',
            'index'  => Magestore_Barcodesuccess_Model_Barcode::PRODUCT_SKU,
        ));

        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::QTY, array(
            'header' => Mage::helper('barcodesuccess')->__('Qty'),
            'width'  => '100px',
            'index'  => Magestore_Barcodesuccess_Model_Barcode::QTY,
        ));

        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::SUPPLIER_CODE, array(
            'header' => Mage::helper('barcodesuccess')->__('Supplier'),
            'width'  => '250px',
            'index'  => Magestore_Barcodesuccess_Model_Barcode::SUPPLIER_CODE,
        ));

        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::PURCHASED_TIME, array(
            'header' => Mage::helper('barcodesuccess')->__('Purchased Time'),
            'width'  => '250px',
            'index'  => Magestore_Barcodesuccess_Model_Barcode::PURCHASED_TIME,
            'type'   => 'datetime',
        ));

        $this->addColumn('action',
                         array(
                             'header'    => Mage::helper('barcodesuccess')->__('Detail'),
                             'width'     => '100',
                             'type'      => 'action',
                             'getter'    => 'getId',
                             'actions'   => array(
                                 array(
                                     'caption' => Mage::helper('barcodesuccess')->__('View'),
                                     'url'     => array('base' => '*/barcodesuccess_barcode/view'),
                                     'field'   => 'id',
                                 ),
                             ),
                             'filter'    => false,
                             'sortable'  => false,
                             'index'     => 'stores',
                             'is_system' => true,
                         ));

        return parent::_prepareColumns();
    }

    /**
     * prepare mass action for this grid
     *
     * @return Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Grid
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * @param $row
     * @return string
     */
    public function getRowUrl( $row )
    {
        return $this->getUrl('*/barcodesuccess_barcode/view', array('id' => $row->getId()));
    }


    /**
     * get grid ajax url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/historyviewgrid', array(
            '_current' => true,
        ));
    }
}