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
class Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Grid extends
    Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('barcodeGrid');
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
        $collection = Mage::getModel('barcodesuccess/barcode')->getCollection();
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
        if ( !$this->_isExport ) {
            $this->addColumn('in_barcodelist', array(
                'header_css_class' => 'a-center',
                'width'            => '50px',
                'type'             => 'checkbox',
                'name'             => 'in_barcodelist',
                'align'            => 'center',
                'index'            => Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID,
            ));
            $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID, array(
                'header'           => Mage::helper('barcodesuccess')->__(' '),
                'width'            => '50px',
                'align'            => 'right',
                'index'            => Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID,
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display',
                'type'             => 'input',
            ));
        }
        $this->addColumn('barcode_id_label', array(
            'header' => Mage::helper('barcodesuccess')->__('ID'),
            'width'  => '250px',
            'type'   => 'number',
            'align'  => 'right',
            'index'  => Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID,
        ));

        $this->addColumn('barcode_label', array(
            'header'           => Mage::helper('barcodesuccess')->__('Barcode'),
            'width'            => '250px',
            'align'            => 'left',
            'index'            => Magestore_Barcodesuccess_Model_Barcode::BARCODE,
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
        ));
        if ( !$this->_isExport ) {
            $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::BARCODE, array(
                'header' => Mage::helper('barcodesuccess')->__('Barcode'),
                'width'  => '150px',
                'align'  => 'left',
                'type'   => 'input',
                'index'  => Magestore_Barcodesuccess_Model_Barcode::BARCODE,
            ));
        }

        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::PRODUCT_SKU, array(
            'header' => Mage::helper('barcodesuccess')->__('SKU'),
            'width'  => '50px',
            'index'  => Magestore_Barcodesuccess_Model_Barcode::PRODUCT_SKU,
            'type'   => $this->_isExport ? 'text' : 'input',
        ));

        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::SUPPLIER_CODE, array(
            'header' => Mage::helper('barcodesuccess')->__('Supplier'),
            'width'  => '50px',
            'type'   => $this->_isExport ? 'text' : 'input',
            'index'  => Magestore_Barcodesuccess_Model_Barcode::SUPPLIER_CODE,
        ));

        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::PURCHASED_TIME, array(
            'header' => Mage::helper('barcodesuccess')->__('Purchased Time'),
            'type'   => 'datetime',
            'index'  => Magestore_Barcodesuccess_Model_Barcode::PURCHASED_TIME,
        ));

        $this->addColumn('action',
                         array(
                             'header'    => Mage::helper('barcodesuccess')->__('Detail'),
                             'type'      => 'action',
                             'getter'    => 'getId',
                             'actions'   => array(
                                 array(
                                     'caption' => Mage::helper('barcodesuccess')->__('View'),
                                     'url'     => array('base' => '*/*/view'),
                                     'field'   => 'id',
                                 ),
                             ),
                             'filter'    => false,
                             'sortable'  => false,
                             'index'     => 'stores',
                             'is_system' => true,
                         ));

        $this->addExportType('*/*/exportCsv', Mage::helper('barcodesuccess')->__('CSV'));
//        $this->addExportType('*/*/exportXml', Mage::helper('barcodesuccess')->__('XML'));

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
        return '';
    }


    /**
     * get grid ajax url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array(
            '_current' => true,
        ));
    }
}