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
class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Requeststock_Edit_Tab_Delivery
    extends
    Mage_Adminhtml_Block_Widget_Grid
{

    protected $_selectedProducts;

    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Requeststock_Edit_Tab_Delivery constructor.
     */
    public function __construct()
    {
        $transfer = $this->_getTransfer();
        if ( $transfer->getId() && $transfer->getStatus() == Magestore_Inventorysuccess_Model_Transferstock::STATUS_COMPLETED ) {
            return;
        }
        parent::__construct();
        $this->setId('delivery');
        $this->setDefaultSort('transferstock_product_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    public function _prepareLayout()
    {
        $this->setChild('save_delivery',
                        $this->getLayout()->createBlock('adminhtml/widget_button')
                             ->setData(array(
                                           'label'   => Mage::helper('adminhtml')->__('Save Delivery'),
                                           'onclick' => "editForm.submit($('edit_form').action+'back/edit/step/save_delivery');",
                                           'class'   => 'save',
                                       ))
        );
        $this->setChild('import',
                        $this->getLayout()->createBlock('adminhtml/widget_button')
                             ->setData(array(
                                           'label'   => Mage::helper('adminhtml')->__('Import'),
                                           'onclick' => "jQuery('#import-request-delivery').modal()",
                                           'class'   => 'import-csv',
                                       ))
        );

        $handleBarcodeUrl = Mage::helper('adminhtml')->getUrl(
            'adminhtml/inventorysuccess_transferstock_requeststock/handleBarcodeDelivery',
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
            $html .= $this->getChildHtml('save_delivery');
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
        return $transfer->getStatus() != Magestore_Inventorysuccess_Model_Transferstock::STATUS_COMPLETED && Mage::helper('inventorysuccess')->hasPermission($transfer->getData('source_warehouse_id'));
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection */
        $collection = Mage::getModel('inventorysuccess/transferstock_product')->getCollection()
                          ->addFieldToFilter('transferstock_id', $this->getRequest()->getParam('id'));
        $collection->getSelect()->join(array('wp' => $collection->getTable('inventorysuccess/warehouse_product')),
                                       'main_table.product_id = wp.product_id',
                                       array('total_qty' => 'total_qty', 'available_qty' => 'qty'));
        /* calculate available qty */
        //$collection->getSelect()->columns(array('available_qty' => 'wp.qty'));
        /* where warehouse id */
        $warehouseId = $this->_getTransfer()->getSourceWarehouseId();
        $collection->getSelect()->where('wp.' . Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID . ' = ?', $warehouseId);
        $collection ->getSelect()->where('(main_table.qty - main_table.qty_delivered) > ? ',0);
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
        if ($column->getId() == 'in_delivery') {
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
        $this->addColumn('in_delivery', array(
            'header_css_class' => 'a-center',
            'type'             => 'checkbox',
            'name'             => 'in_delivery',
            'align'            => 'center',
            'index'            => 'transferstock_product_id',
            'values'           => $this->getSelectedProducts(),
            'filter'       => false
        ));

        $this->addColumn('product_id_label', array(
            'header' => Mage::helper('catalog')->__('ID'),
            'align'  => 'left',
            'index'  => 'product_id',
            'filter_condition_callback' => array($this, '_filterIDCallback')
        ));

        $this->addColumn('product_id', array(
            'header'           => Mage::helper('inventorysuccess')->__(' '),
            'type'             => 'input',
            'index'            => 'product_id',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
            'editable'         => true,
            'is_system'        => true,
        ));

        $this->addColumn('product_sku_label', array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'align'  => 'left',
            'index'  => 'product_sku',
        ));

        $this->addColumn('product_sku', array(
            'header'           => Mage::helper('inventorysuccess')->__(' '),
            'type'             => 'input',
            'index'            => 'product_sku',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
            'editable'         => true,
            'is_system'        => true,
        ));

        $this->addColumn('product_name_label', array(
            'header' => Mage::helper('catalog')->__('Name'),
            'align'  => 'left',
            'index'  => 'product_name',
        ));

        $this->addColumn('product_name', array(
            'header'           => Mage::helper('inventorysuccess')->__(' '),
            'type'             => 'input',
            'index'            => 'product_name',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
            'editable'         => true,
            'is_system'        => true,
        ));

        $this->addColumn('product_request_label', array(
            'header' => Mage::helper('catalog')->__('Qty Requested'),
            'align'  => 'left',
            'index'  => 'qty',
            'type'   => 'number',
        ));

        $this->addColumn('qty', array(
            'header'           => Mage::helper('inventorysuccess')->__(' '),
            'type'             => 'input',
            'index'            => 'qty',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
            'editable'         => true,
            'is_system'        => true,
        ));

//        $this->addColumn('qty', array(
//            'header' => Mage::helper('catalog')->__('Qty Requested'),
//            'width'  => '20px',
//            'name'   => 'qty',
//            'type'   => 'number',
//            'index'  => 'qty',
//        ));

        $this->addColumn('product_delivery_label', array(
            'header' => Mage::helper('catalog')->__('Qty Delivered'),
            'align'  => 'left',
            'index'  => 'qty_delivered',
            'type'   => 'number',
        ));

        $this->addColumn('qty_delivered', array(
            'header'           => Mage::helper('inventorysuccess')->__(' '),
            'type'             => 'input',
            'index'            => 'qty_delivered',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
            'editable'         => true,
            'is_system'        => true,
        ));

//        $this->addColumn('qty_delivered', array(
//            'header' => Mage::helper('catalog')->__('Qty Delivered'),
//            'width'  => '20px',
//            'index'  => 'qty_delivered',
//            'type'   => 'number',
//            'name'   => 'qty_delivered',
//        ));

        $this->addColumn('qty_received', array(
            'header' => Mage::helper('catalog')->__('Qty Received'),
            'width'  => '20px',
            'index'  => 'qty_received',
            'type'   => 'number',
            'name'   => 'qty_received',
        ));
        $this->addColumn('available_qty', array(
            'header' => Mage::helper('catalog')->__('Qty in Warehouse'),
            'width'  => '20px',
            'index'  => 'available_qty',
            'type'   => 'number',
            'name'   => 'available_qty',
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

    public function _filterIDCallback($collection, $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }
        $collection->getSelect()->where('wp.product_id like ?', $value);
    }
    /**
     * @return mixed
     */
    public function _getTransfer()
    {
        return Mage::getModel('inventorysuccess/transferstock')->load($this->getRequest()->getParam('id'));
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
        return $this->getUrl('*/*/deliveryGrid', array(
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
     * @return mixed
     */
    public function getSelectedProducts()
    {
        if ($products = $this->getRequest()->getParam('products_selected'))
            return $products;
        if ( !$this->_selectedProducts ) {
            $data = array();
            if ( Mage::getSingleton('adminhtml/session')->getData('request_delivery_products') ) {
                $data = Mage::getSingleton('adminhtml/session')->getData('request_delivery_products');
            }
            $this->_selectedProducts = $data;
        }
        return array_keys($this->_selectedProducts);
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
     * @return array
     */
    public function getSelectedProductIds()
    {
        $products = $this->getSelectedProducts();
        $ids      = array();
        if ( count($products) ) {
            foreach ( $products as $product ) {
                $ids[] = $product['product_id'];
            }
        }
        return $ids;
    }

}