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
class Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Generate_Edit_Tab_Grid extends
    Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Generate_Edit_Tab_Grid constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('entity_id');
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
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('thumbnail');
        $collection->addAttributeToSelect('price');
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
        $this->addColumn('in_productlist', array(
            'header_css_class' => 'a-center',
            'width'            => '50px',
            'type'             => 'checkbox',
            'name'             => 'in_productlist',
            'align'            => 'center',
            'index'            => 'entity_id',
        ));
        $oneBarcodePerSku = Mage::helper('barcodesuccess')->isOneBarcodePerSku();

        $this->addColumn('product_id_label', array(
            'header' => Mage::helper('catalog')->__('ID'),
            'width'  => '50px',
            'align'  => 'left',
            'index'  => 'entity_id',
        ));

        $this->addColumn('product_id', array(
            'header'           => Mage::helper('barcodesuccess')->__(' '),
            'width'            => '200px',
            'type'             => 'input',
            'index'            => 'entity_id',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
            'editable'         => true,
            'is_system'        => true,
        ));

        $this->addColumn('product_sku_label', array(
            'width'  => '100px',
            'header' => Mage::helper('catalog')->__('SKU'),
            'align'  => 'left',
            'index'  => 'sku',
        ));

        $this->addColumn('product_sku', array(
            'header'           => Mage::helper('barcodesuccess')->__(' '),
            'width'            => '200px',
            'type'             => 'input',
            'index'            => 'sku',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
            'editable'         => true,
            'is_system'        => true,
        ));

        $this->addColumn('thumbnail', array(
            'header'   => Mage::helper('barcodesuccess')->__('Thumbnail'),
            'align'    => 'left',
            'width'    => '50px',
            'index'    => 'thumbnail',
            'filter'   => false,
            'renderer' => 'barcodesuccess/adminhtml_barcode_generate_renderer_thumbnail',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('barcodesuccess')->__('Product Name'),
            'align'  => 'left',
            'width'  => '150px',
            'index'  => 'name',
        ));

        $this->addColumn('price', array(
            'header' => Mage::helper('barcodesuccess')->__('Price'),
            'type'   => 'number',
            'align'  => 'left',
            'width'  => '50px',
            'index'  => 'price',
        ));

        $this->addColumn('qty', array(
            'header'   => Mage::helper('barcodesuccess')->__('Available Qty'),
            'type'     => 'number',
            'align'    => 'left',
            'width'    => '200px',
            'renderer' => 'barcodesuccess/adminhtml_barcode_generate_renderer_qty',
        ));

        $this->addColumn('supplier', array(
            'header'    => Mage::helper('barcodesuccess')->__('Supplier'),
            'type'      => 'input',
            'align'     => 'left',
            'filter'    => false,
            'editable'  => true,
            'edit_only' => true,
        ));
        if ( !$oneBarcodePerSku ) {
            $this->addColumn('item_qty', array(
                'header'     => Mage::helper('barcodesuccess')->__('Item Quantity'),
                'type'       => 'input',
                'align'      => 'left',
                'filter'     => false,
                'editable'   => true,
                'edit_only'  => true,
                'inline_css' => 'validate-number validate-zero-or-greater',
            ));
            $this->addColumn('purchased_time', array(
                'header'     => Mage::helper('barcodesuccess')->__('Purchased Time'),
                'type'       => 'input',
                'align'      => 'left',
                'filter'     => false,
                'editable'   => true,
                'edit_only'  => true,
                'inline_css' => '',
            ));
        }

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
        return $this->getUrl('*/*/productgrid', array(
            '_current' => true,
        ));
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        $script = "<script>jQuery('input[name=purchased_time]').datetimepicker({format:'Y-m-d H:i',})</script>";
        return parent::_toHtml() . $script;
    }
}