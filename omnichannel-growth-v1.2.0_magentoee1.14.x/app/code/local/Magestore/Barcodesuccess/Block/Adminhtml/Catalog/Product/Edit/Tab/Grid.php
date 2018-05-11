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
class Magestore_Barcodesuccess_Block_Adminhtml_Catalog_Product_Edit_Tab_Grid extends
    Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * Magestore_Barcodesuccess_Block_Adminhtml_Catalog_Product_Edit_Tab_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('barcodeTabGrid');
        $this->setDefaultSort(Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID);
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }


    /**
     * @return Mage_Core_Block_Abstract
     */
    public function _prepareLayout()
    {
        $this->setChild('print_button',
                        $this->getLayout()->createBlock('adminhtml/widget_button')
                             ->setData(array(
                                           'label'   => Mage::helper('adminhtml')->__('Print Selected Barcode'),
                                           'onclick' => "productBarcodeForm.printSelected()",
                                           'class'   => '',
                                       ))
        );
        $this->setChild('delete_button',
                        $this->getLayout()->createBlock('adminhtml/widget_button')
                             ->setData(array(
                                           'label'   => Mage::helper('adminhtml')->__('Delete Selected Barcode'),
                                           'onclick' => "productBarcodeForm.deleteSelected()",
                                           'class'   => '',
                                       ))
        );
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    protected function getPrintButtonHtml()
    {
        return $this->getChildHtml('print_button');
    }


    /**
     * @return string
     */
    protected function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $html = $this->getDeleteButtonHtml();
        $html .= $this->getPrintButtonHtml();
        $html .= parent::getMainButtonsHtml();
        return $html;
    }

    /**
     * prepare collection for block to display
     *
     * @return Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Grid
     */
    protected function _prepareCollection()
    {
        $productId  = $this->getRequest()->getParam('id');
        $collection = Mage::getModel('barcodesuccess/barcode')->getCollection()
                          ->addFieldToFilter(Magestore_Barcodesuccess_Model_Barcode::PRODUCT_ID, $productId);
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
        $this->addColumn('in_barcodelist', array(
            'header_css_class' => 'a-center',
            'type'             => 'checkbox',
            'name'             => 'in_barcodelist',
            'align'            => 'center',
            'index'            => Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID,
        ));

        $this->addColumn('barcode_id_label', array(
            'header' => Mage::helper('barcodesuccess')->__('ID'),
            'align'  => 'right',
            'index'  => Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID,
            'width'  => '50px',
        ));
        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID, array(
            'header'           => Mage::helper('barcodesuccess')->__(''),
            'type'             => 'input',
            'align'            => 'right',
            'index'            => Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID,
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
        ));

        $this->addColumn('barcode_label', array(
            'header'           => Mage::helper('barcodesuccess')->__(''),
            'align'            => 'left',
            'index'            => Magestore_Barcodesuccess_Model_Barcode::BARCODE,
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
        ));
        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::BARCODE, array(
            'header' => Mage::helper('barcodesuccess')->__('Barcode'),
            'type'   => 'input',
            'index'  => Magestore_Barcodesuccess_Model_Barcode::BARCODE,
        ));
        $this->addColumn('print_qty', array(
            'header'     => Mage::helper('barcodesuccess')->__('Print Qty'),
            'index'      => 'print_qty',
            'filter'     => false,
            'type'       => 'input',
            'inline_css' => 'validate-number validate-zero-or-greater',
        ));
        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::PRODUCT_SKU, array(
            'header' => Mage::helper('barcodesuccess')->__('SKU'),
            'index'  => Magestore_Barcodesuccess_Model_Barcode::PRODUCT_SKU,
        ));

        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::SUPPLIER_CODE, array(
            'header' => Mage::helper('barcodesuccess')->__('Supplier'),
            'index'  => Magestore_Barcodesuccess_Model_Barcode::SUPPLIER_CODE,
        ));

        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::PURCHASED_TIME, array(
            'header' => Mage::helper('barcodesuccess')->__('Purchased Time'),
            'type'   => 'date',
            'index'  => Magestore_Barcodesuccess_Model_Barcode::PURCHASED_TIME,
        ));

        $this->addColumn(Magestore_Barcodesuccess_Model_Barcode::CREATED_AT, array(
            'header' => Mage::helper('barcodesuccess')->__('Created At'),
            'type'   => 'date',
            'index'  => Magestore_Barcodesuccess_Model_Barcode::CREATED_AT,
        ));

        $this->addColumn('action',
                         array(
                             'header'    => Mage::helper('barcodesuccess')->__('Action'),
                             'type'      => 'action',
                             'getter'    => 'getId',
                             'actions'   => array(
                                 array(
                                     'caption' => Mage::helper('barcodesuccess')->__('Print'),
                                     'onclick' => 'productBarcodeForm.printItem(this);',
                                     'field'   => 'id',
                                     'style'   => 'cursor:pointer',
                                 ),
                             ),
                             'filter'    => false,
                             'sortable'  => false,
                             'is_system' => true,
                         ));
        $this->addColumn('view',
                         array(
                             'header'    => Mage::helper('barcodesuccess')->__('Detail'),
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
        return '';
    }


    /**
     * get grid ajax url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/barcodesuccess_product/productbarcodegrid', array(
            '_current' => true,
        ));
    }
}