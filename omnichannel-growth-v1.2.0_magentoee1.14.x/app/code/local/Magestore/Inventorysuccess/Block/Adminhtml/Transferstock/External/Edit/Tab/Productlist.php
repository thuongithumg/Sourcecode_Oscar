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

/**
 * Inventorysuccess Edit Form Content Tab Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_External_Edit_Tab_Productlist
    extends
    Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var Magestore_Inventorysuccess_Model_Transferstock
     */
    protected $_transfer;

    protected $_selectedProducts;

    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_External_Edit_Tab_Productlist constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('external_productList');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        if ( count($this->getSelectedProducts()) ) {
            $this->setDefaultFilter(array('in_productlist' => 1));
        }
    }

    /**
     * @return Mage_Core_Block_Abstract
     */
    public function _prepareLayout()
    {
        $this->setChild('import_button',
                        $this->getLayout()->createBlock('adminhtml/widget_button')
                             ->setData(array(
                                           'label'   => Mage::helper('adminhtml')->__('Import CSV file'),
                                           'onclick' => "jQuery('#import-external').modal()",
                                           'class'   => 'import-csv',
                                       ))
        );

        $handleBarcodeUrl = Mage::helper('adminhtml')->getUrl(
            'adminhtml/inventorysuccess_transferstock_external/handleBarcode',
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
    protected function getImportButtonHtml()
    {
        return $this->getChildHtml('import_button');
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
        $html .= $this->getImportButtonHtml();
        $html .= parent::getMainButtonsHtml();
        return $html;
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
        if ( $column->getId() == 'in_productlist' ) {
            $productIds = $this->_getSelectedProducts();
            if ( empty($productIds) ) {
                $productIds = 0;
            }
            if ( $column->getFilter()->getValue() ) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } else {
                if ( $productIds ) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        /** @var Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection */
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addAttributeToSelect('name');
        $collection->getSelect()->join(array('wp' => $collection->getTable('inventorysuccess/warehouse_product')),
                                       'e.entity_id = wp.product_id',
                                       array('total_qty' => 'total_qty', 'available_qty' => 'qty'));
        /* calculate available qty */
        //$collection->getSelect()->columns(array('available_qty' => new Zend_Db_Expr('`wp`.`total_qty` - `wp`.`qty_to_ship`')));
        /* where warehouse id */
        if ( $this->getTransfer()->getType() == Magestore_Inventorysuccess_Model_Transferstock::TYPE_TO_EXTERNAL ) {
            $warehouseId = $this->getTransfer()->getSourceWarehouseId();
        } else {
            $warehouseId = $this->getTransfer()->getDesWarehouseId();
        }
        $collection->getSelect()->where('wp.' . Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID . ' = ?', $warehouseId);
        Mage::getSingleton('inventorysuccess/service_filterCollection_filter')->mappingAttribute($collection,'wp.product_id');

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_productlist', array(
            'header_css_class' => 'a-center',
            'type'             => 'checkbox',
            'name'             => 'in_productlist',
            'align'            => 'center',
            'index'            => 'entity_id',
            'values'           => $this->_getSelectedProducts(),
        ));

        $this->addColumn('product_id_label', array(
            'header' => Mage::helper('catalog')->__('ID'),
            'align'  => 'left',
            'index'  => 'entity_id',
        ));

        $this->addColumn('product_id', array(
            'header'           => Mage::helper('inventorysuccess')->__(' '),
            'type'             => 'input',
            'index'            => 'entity_id',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
            'editable'         => true,
            'is_system'        => true,
        ));

        $this->addColumn('product_sku_label', array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'align'  => 'left',
            'index'  => 'sku',
        ));

        $this->addColumn('product_sku', array(
            'header'           => Mage::helper('inventorysuccess')->__(' '),
            'type'             => 'input',
            'index'            => 'sku',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
            'editable'         => true,
            'is_system'        => true,
        ));

        $this->addColumn('product_name_label', array(
            'header' => Mage::helper('catalog')->__('Name'),
            'align'  => 'left',
            'index'  => 'name',
        ));

        $this->addColumn('product_name', array(
            'header'           => Mage::helper('inventorysuccess')->__(' '),
            'type'             => 'input',
            'index'            => 'name',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
            'editable'         => true,
            'is_system'        => true,
        ));
        $this->addColumn('available_qty', array(
            'header' => Mage::helper('catalog')->__('Qty in Warehouse'),
            'align'  => 'right',
            'width'  => '150px',
            'index'  => 'available_qty',
            'filter' => false,
            'sort'   => false,
            'type'   => 'number',
        ));
        $this->addColumn('qty', array(
            'header'     => Mage::helper('catalog')->__('Qty to Transfer'),
            'index'      => 'qty',
            'type'       => 'input',
            'name'       => 'qty',
            'filter'     => false,
            'editable'   => true,
            'edit_only'  => true,
            'width'      => '20px',
            'inline_css' => 'validate-number validate-greater-than-zero',
        ));

        $this->addColumn('status',
            array(
                'header'    => $this->__('Status'),
                'align'     =>'left',
                'index'     => 'status',
                'type'      => 'options',
                'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray(),
            ));

        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/productlistgrid', array(
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
    protected function _getSelectedProducts()
    {
        if ($products = $this->getRequest()->getParam('products_selected'))
            return $products;
        return array_keys($this->getSelectedProducts());
    }

    /**
     * @return array
     */
    public function getSelectedProducts()
    {
        if ( !$this->_selectedProducts ) {
            $transfer = $this->getTransfer();
            $service  = Magestore_Coresuccess_Model_Service::transferStockService();
            $products = $service->getProducts($transfer);
            $data     = array();
            if ( $products->getSize() ) {
                /** @var Magestore_Inventorysuccess_Model_Transferstock_Product $product */
                foreach ( $products as $product ) {
                    $data[$product->getProductId()] = array(
                        'product_id'   => $product->getProductId(),
                        'product_name' => $product->getProductName(),
                        'product_sku'  => $product->getProductSku(),
                        'qty'          => $product->getQty(),
                    );
                }
            } elseif ( Mage::getSingleton('adminhtml/session')->getData('external_products') ) {
                $data = Mage::getSingleton('adminhtml/session')->getData('external_products');
            }
            $this->_selectedProducts = $data;
        }
        return $this->_selectedProducts;

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
}
