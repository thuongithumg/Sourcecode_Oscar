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
class Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Print_Edit_Tab_Grid extends
    Mage_Adminhtml_Block_Widget_Grid
{
    protected $_selectedProducts;
    protected $_selectedBarcodes;

    /**
     * Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Print_Edit_Tab_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('barcodePrintGrid');
        $this->setDefaultSort(Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID);
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ( count($this->getSelectedProducts()) || count($this->getMassprintBarcodes()) ) {
            $this->setDefaultFilter(array('in_barcodelist' => 1));
        }
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('barcodesuccess/barcode')->getCollection();
        if ( $this->getHistoryId() ) {
            $collection->addFieldToFilter(Magestore_Barcodesuccess_Model_Barcode::HISTORY_ID, $this->getHistoryId());
        }
        $selectedProducts = $this->getSelectedProducts();
        if ( $selectedProducts && count($selectedProducts) ) {
            $collection->addFieldToFilter(Magestore_Barcodesuccess_Model_Barcode::PRODUCT_ID, array('in' => $selectedProducts));
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add filter
     *
     * @param object $column
     * @return $this
     */
    protected function _addColumnFilterToCollection( $column )
    {
        // Set custom filter for in product flag
        if ( $column->getId() == 'in_barcodelist' ) {
//            $productIds = $this->getSelectedProducts();
//            if ( empty($productIds) ) {
//                $productIds = 0;
//            }
            $barcodeIds = $this->getMassprintBarcodes();
            if ( empty($barcodeIds) ) {
                $barcodeIds = 0;
            }
            if ( $column->getFilter()->getValue() ) {
//                if ( $productIds ) {
//                    $this->getCollection()->addFieldToFilter(Magestore_Barcodesuccess_Model_Barcode::PRODUCT_ID, array('in' => $productIds));
//                }
                if ( $barcodeIds ) {
                    $this->getCollection()->addFieldToFilter(Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID, array('in' => $barcodeIds));
                }
            } else {
//                if ( $productIds ) {
//                    $this->getCollection()->addFieldToFilter(Magestore_Barcodesuccess_Model_Barcode::PRODUCT_ID, array('nin' => $productIds));
//                }
                if ( $barcodeIds ) {
                    $this->getCollection()->addFieldToFilter(Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID, array('nin' => $barcodeIds));
                }
            }

        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * prepare columns for this grid
     *
     * @return Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_barcodelist', array(
            'header_css_class' => 'a-center',
            'width'            => '50px',
            'type'             => 'checkbox',
            'name'             => 'in_barcodelist',
            'align'            => 'center',
            'index'            => Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID,
            'values'           => $this->getSelectedBarcodes(),
        ));
        $this->addColumn('qty', array(
            'header'     => Mage::helper('barcodesuccess')->__('Print QTY'),
            'type'       => 'input',
            'align'      => 'left',
            'width'      => '100px',
            'filter'     => false,
            'index'      => 'qty',
            'inline_css' => 'validate-number validate-greater-than-zero',
        ));
        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID, array(
            'header' => Mage::helper('barcodesuccess')->__('ID'),
            'align'  => 'left',
            'width'  => '50px',
            'type'   => 'number',
            'index'  => Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID,
        ));
        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::BARCODE, array(
            'header' => Mage::helper('barcodesuccess')->__('Barcode'),
            'align'  => 'left',
            'width'  => '150px',
            'index'  => Magestore_Barcodesuccess_Model_Barcode::BARCODE,
        ));
        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::PRODUCT_SKU, array(
            'header' => Mage::helper('barcodesuccess')->__('SKU'),
            'align'  => 'left',
            'width'  => '150px',
            'index'  => Magestore_Barcodesuccess_Model_Barcode::PRODUCT_SKU,
        ));
        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::SUPPLIER_CODE, array(
            'header' => Mage::helper('barcodesuccess')->__('Supplier'),
            'align'  => 'left',
            'width'  => '150px',
            'index'  => Magestore_Barcodesuccess_Model_Barcode::SUPPLIER_CODE,
        ));
        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::PURCHASED_TIME, array(
            'header' => Mage::helper('barcodesuccess')->__('Purchased Time'),
            'type'   => 'date',
            'time'   => true,
            'align'  => 'left',
            'width'  => '150px',
            'index'  => Magestore_Barcodesuccess_Model_Barcode::PURCHASED_TIME,
        ));
        $this->addColumn('action',
                         array(
                             'header'   => $this->__('Action'),
                             'width'    => '100',
                             'type'     => 'action',
                             'getter'   => 'getId',
                             'actions'  => array(
                                 array(
                                     'caption' => $this->__('View'),
                                     'url'     => array('base' => '*/barcodesuccess_barcode/view'),
                                     'field'   => 'id',
                                 ),
                             ),
                             'filter'   => false,
                             'sortable' => false,
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

    /**
     * data from mass print in product grid
     * @return array
     */
    public function getSelectedProducts()
    {
        if ( $this->_selectedProducts ) {
            return $this->_selectedProducts;
        }
        $productIds = Mage::getSingleton('adminhtml/session')->getData('print_products', true);
        if ( $productIds ) {
            $this->_selectedProducts = $productIds;
            return $productIds;
        }
        return array();
    }

    /**
     * data from mass print in barcode grid
     * @return array
     */
    public function getMassprintBarcodes()
    {
        if ( $this->_selectedBarcodes ) {
            return $this->_selectedBarcodes;
        }
        $barcodes = Mage::getSingleton('adminhtml/session')->getData('massprint_barcodes', true);
        if ( $barcodes ) {
            $this->_selectedBarcodes = $barcodes;
            return array_keys($barcodes);
        }
        return array();
    }

    /**
     * @return mixed
     */
    public function getSelectedBarcodes()
    {
//        if ( $this->getSelectedProducts() ) {
//            $barcodeCollection = Mage::getModel('barcodesuccess/barcode')->getCollection()
//                                     ->addFieldToFilter(Magestore_Barcodesuccess_Model_Barcode::PRODUCT_ID, array('in' => $this->getSelectedProducts()));
//            return $barcodeCollection->getAllIds();
//        }
        if ( $this->getMassprintBarcodes() ) {
            $barcodeCollection = Mage::getModel('barcodesuccess/barcode')->getCollection()
                                     ->addFieldToFilter(Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID, array('in' => $this->getMassprintBarcodes()));
            return $barcodeCollection->getAllIds();
        }

    }

    /**
     * @return array
     */
    public function getSelected()
    {
        $barcodeIds = $this->getSelectedBarcodes();
        $data       = array();
        if ( count($barcodeIds) ) {
            foreach ( $barcodeIds as $barcodeId ) {
                $data[$barcodeId] = array('qty' => '1');
            }
        }
        return $data;
    }

    /**
     * @return mixed
     */
    public function getHistoryId()
    {
        return $this->getRequest()->getParam('historyId');
    }
}