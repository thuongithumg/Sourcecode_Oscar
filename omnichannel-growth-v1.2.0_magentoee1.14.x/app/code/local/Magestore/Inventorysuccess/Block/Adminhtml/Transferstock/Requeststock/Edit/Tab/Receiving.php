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
class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Requeststock_Edit_Tab_Receiving
    extends
    Mage_Adminhtml_Block_Widget_Grid
{

    protected $_selectedProducts;

    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Requeststock_Edit_Tab_Receiving constructor.
     */
    public function __construct()
    {
        $transfer = Mage::getModel('inventorysuccess/transferstock')->load($this->getRequest()->getParam('id'));
        if ( $transfer->getId() && $transfer->getStatus() == Magestore_Inventorysuccess_Model_Transferstock::STATUS_COMPLETED ) {
            return;
        }
        parent::__construct();
        $this->setId('receiving');
        $this->setDefaultSort('transferstock_product_id');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    public function _prepareLayout()
    {
        $this->setChild('save_receiving',
                        $this->getLayout()->createBlock('adminhtml/widget_button')
                             ->setData(array(
                                           'label'   => Mage::helper('adminhtml')->__('Save Receiving'),
                                           'onclick' => "editForm.submit($('edit_form').action+'back/edit/step/save_receiving');",
                                           'class'   => 'save',
                                       ))
        );
        $this->setChild('import',
                        $this->getLayout()->createBlock('adminhtml/widget_button')
                             ->setData(array(
                                           'label'   => Mage::helper('adminhtml')->__('Import'),
                                           'onclick' => "jQuery('#import-request-receiving').modal();",
                                           'class'   => 'import-csv',
                                       ))
        );

        $handleBarcodeUrl = Mage::helper('adminhtml')->getUrl(
            'adminhtml/inventorysuccess_transferstock_requeststock/handleBarcodeReceiving',
            array('transferstock_id' => $this->getTransfer()->getId())
        );
        $this->setChild('scanbarcode_button',
                        $this->getLayout()->createBlock('adminhtml/widget_button')
                             ->setData(array(
                                           'label'   => Mage::helper('adminhtml')->__('Scan Barcode'),
                                           'onclick' => "jQuery('#scan-barcode').modal();scanBarcode.handleUrl='$handleBarcodeUrl';",
                                           'class'   => '',
                                       ))
        );
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    protected function getScanBarcodeButtonHtml()
    {
        if ( Magestore_Inventorysuccess_Helper_Data::isBarcodeInstalled() ) {
            return $this->getChildHtml('scanbarcode_button');
        }
        return '';
    }

    /**
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $html = $this->getScanBarcodeButtonHtml();
        if($this->editAble()) {
            $html .= $this->getChildHtml('import');
            $html .= $this->getChildHtml('save_receiving');
        }
        $html .= parent::getMainButtonsHtml();
        return $html;
    }

    public function isComplete(){
        $transfer = Mage::getModel('inventorysuccess/transferstock')->load(Mage::app()->getRequest()->getParam('id'));
        return $transfer->getStatus() == Magestore_Inventorysuccess_Model_Transferstock::STATUS_COMPLETED;
    }

    public function editAble(){
        $transfer = Mage::getModel('inventorysuccess/transferstock')->load(Mage::app()->getRequest()->getParam('id'));
        return $transfer->getStatus() != Magestore_Inventorysuccess_Model_Transferstock::STATUS_COMPLETED && Mage::helper('inventorysuccess')->hasPermission($transfer->getData('des_warehouse_id'));
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection */
        $collection = Mage::getModel('inventorysuccess/transferstock_product')->getCollection()
                          ->addFieldToFilter('transferstock_id', $this->getRequest()->getParam('id'));
        $collection->getSelect()->where('(qty_delivered - qty_received - qty_returned) > ? ',0);

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    /**
     * prepare column to filter
     *
     * @param $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_receiving') {
            $transfer_productIds = $this->getSelectedProducts();
            if (empty($transfer_productIds)) {
                $transfer_productIds = 0;
            }
            if ($column->getFilter()->getValue() == 1) {
                $this->getCollection()->addFieldToFilter('transferstock_product_id', array('in' => $transfer_productIds));
            } elseif($column->getFilter()->getValue() == 0) {
                $this->getCollection()->addFieldToFilter('transferstock_product_id', array('nin' => $transfer_productIds));
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_receiving', array(
            'header_css_class' => 'a-center',
            'type'             => 'checkbox',
            'align'            => 'center',
            'index'            => 'transferstock_product_id',
            'values'           => $this->getSelectedProducts(),
            'filter'       => false
        ));

        $this->addColumn('product_id', array(
            'header'    => Mage::helper('catalog')->__('Product ID'),
            'width'     => '150px',
            'align'     => 'center',
            'type'      => 'number',
            'index'     => 'product_id',
            'name'      => 'product_id',
            'editable'  => true,
            'edit_only' => true,
        ));
        $this->addColumn('product_sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'align'     => 'center',
            'width'     => '150px',
            'index'     => 'product_sku',
            'name'      => 'product_sku',
            'editable'  => true,
            'edit_only' => true,
        ));
        $this->addColumn('product_name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'width'     => '350px',
            'align'     => 'left',
            'index'     => 'product_name',
            'name'      => 'product_name',
            'editable'  => true,
            'edit_only' => true,
        ));
        $this->addColumn('qty', array(
            'header' => Mage::helper('catalog')->__('Qty Requested'),
            'width'  => '20px',
            'name'   => 'qty',
            'type'   => 'number',
            'index'  => 'qty',
        ));
        $this->addColumn('qty_delivered', array(
            'header' => Mage::helper('catalog')->__('Qty Delivered'),
            'width'  => '20px',
            'index'  => 'qty_delivered',
            'type'   => 'number',
            'name'   => 'qty_delivered',
            'editable'  => true,
            'edit_only' => true,
        ));
        $this->addColumn('qty_received', array(
            'header' => Mage::helper('catalog')->__('Qty Received'),
            'width'  => '20px',
            'index'  => 'qty_received',
            'type'   => 'number',
            'name'   => 'qty_received',
            'editable'  => true,
            'edit_only' => true,
        ));
        $this->addColumn('qty_returned', array(
            'header' => Mage::helper('catalog')->__('Qty Returned'),
            'width'  => '20px',
            'index'  => 'qty_returned',
            'type'   => 'number',
            'name'   => 'qty_returned',
            'editable'  => true,
            'edit_only' => true,
        ));
        $this->addColumn('new_qty', array(
            'header'     => Mage::helper('catalog')->__('Qty'),
            'type'       => 'input',
            'index'      => 'new_qty',
            'width'      => '20px',
            'editable'   => true,
            'edit_only'  => true,
            'filter'     => false,
            'inline_css' => 'validate-number validate-greater-than-zero',
        ));
        return parent::_prepareColumns();
    }

    /**
     * Grid url getter
     * Version of getGridUrl() but with parameters
     *
     * @param array $params url parameters
     * @return string current grid url
     */
    public function getGridUrl( $params = array() )
    {
        return $this->getUrl('*/*/receivingGrid', array(
            '_current' => true,
        ));
    }

    /**
     * @param $item
     * @return string
     */
    public function getRowUrl( $item )
    {
        return '';
    }

    /**
     * @return array
     */
    public function getSelectedProducts()
    {
        if ($products = $this->getRequest()->getParam('products_selected'))
            return $products;
        if ( !$this->_selectedProducts ) {
            $data = array();
            if ( Mage::getSingleton('adminhtml/session')->getData('request_receiving_products') ) {
                $data = Mage::getSingleton('adminhtml/session')->getData('request_receiving_products');
            }
            $this->_selectedProducts = $data;
        }
        return array_keys($this->_selectedProducts);
    }

    /**
     * get transferstock_product_id
     * @param $productId
     * @return mixed
     */
    protected function getTransferProductId( $productId )
    {
        return Mage::getModel('inventorysuccess/transferstock_product')
                   ->getCollection()
                   ->addFieldToFilter(Magestore_Inventorysuccess_Model_Transferstock_Product::PRODUCT_ID, $productId)
                   ->addFieldToFilter(Magestore_Inventorysuccess_Model_Transferstock_Product::TRANSFERSTOCK_ID, $this->getRequest()->getParam('id'))
                   ->getFirstItem()
                   ->getId();
    }

    /**
     * @return Magestore_Inventorysuccess_Model_Transferstock
     */
    public function getTransfer()
    {
        if ( !$this->_transfer ) {
            $this->_transfer = Mage::getModel('inventorysuccess/transferstock')->load($this->getRequest()->getParam('id'));
        }
        return $this->_transfer;
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        $style = "<style>.editable input{display: none;}.editable input[name='new_qty']{display: inline-block}</style>";
        return parent::_toHtml() . $style;
    }

}