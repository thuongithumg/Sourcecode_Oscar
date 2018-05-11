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
 * @package     Magestore_Suppliersuccess
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Suppliersuccess Edit Form Content Tab Block
 *
 * @category    Magestore
 * @package     Magestore_Suppliersuccess
 * @author      Magestore Developer
 */
class Magestore_Suppliersuccess_Block_Adminhtml_Supplier_Edit_Tab_Products extends
    Mage_Adminhtml_Block_Widget_Grid
{

    /**
     * @var Magestore_Suppliersuccess_Model_Service_Supplier_ImportService
     */
    protected $importService;

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->importService = Magestore_Coresuccess_Model_Service::supplierImportService();
        $this->setId('supplierproductGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        if ( ($this->getSupplier() && $this->getSupplier()->getId())
             || $this->importService->getImportProducts()
        ) {
            $this->setDefaultFilter(array('in_products' => 1));
        }
        if ( !$this->_getSelectedProducts() ) {
            //$this->setDefaultFilter(array('in_products' => 0));
        }
    }

    /**
     *
     * @param type $column
     * @return Magestore_Suppliersuccess_Block_Adminhtml_Supplier_Edit_Tab_Products
     */
    protected function _addColumnFilterToCollection( $column )
    {
        if ( $column->getId() == 'in_products' ) {
            $productIds = $this->_getSelectedProducts();
            if ( empty($productIds) ) {
                $productIds = 0;
            }
            if ( $column->getFilter()->getValue() ) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } elseif ( $productIds ) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
            }
            return $this;
        }
        return parent::_addColumnFilterToCollection($column);
    }

    /**
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('catalog/product')->getCollection()
                          ->addAttributeToSelect(array('name', 'price', 'image'))
                          ->addAttributeToFilter('type_id', array(
                              'nin' => array(
                                  'configurable',
                                  'bundle',
                                  'grouped',
                              ),
                          ));;
        if ( $storeId = $this->getRequest()->getParam('store', 0) ) {
            $collection->addStoreFilter($storeId);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * prepare columns
     */
    protected function _prepareColumns()
    {
        $currencyCode = Mage::app()->getStore()->getBaseCurrency()->getCode();

        $this->addColumn('in_products', array(
            'header_css_class' => 'a-center',
            'type'             => 'checkbox',
            'name'             => 'in_products',
            'values'           => $this->_getSelectedProducts(),
            'align'            => 'center',
            'index'            => 'entity_id',
            'use_index'        => true,
        ));

        $this->addColumn('entity_id', array(
            'header'   => Mage::helper('suppliersuccess')->__('ID'),
            'sortable' => true,
            'width'    => '50px',
            'index'    => 'entity_id',
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('suppliersuccess')->__('Name'),
            'align'  => 'left',
            'index'  => 'name',
            'width'  => '150px',
        ));

        $this->addColumn('product_sku', array(
            'header' => Mage::helper('suppliersuccess')->__('SKU'),
            'width'  => '80px',
            'index'  => 'sku',
        ));
        /*
        $this->addColumn('product_image', array(
            'header' => Mage::helper('suppliersuccess')->__('Image'),
            'width' => '90px',
            'index' => 'product_image',
            'filter' => false,
            'renderer' => 'suppliersuccess/adminhtml_supplier_product_renderer_productimage'
        ));
        */
        $this->addColumn('product_price', array(
            'header'        => Mage::helper('suppliersuccess')->__('Price') .
                               ' (' . Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE) . ')',
            'type'          => 'currency',
            'currency_code' => (string)Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'         => 'price',
            'width'         => '80px',
        ));

        $this->addColumn('product_supplier_sku', array(
            'header'    => Mage::helper('suppliersuccess')->__('Supplier Product SKU'),
            'name'      => 'product_supplier_sku',
            'type'      => 'number',
            'index'     => 'product_supplier_sku',
            'editable'  => true,
            'edit_only' => true,
            'filter'    => false,
            'width'     => '80px',
        ));

        $this->addColumn('cost', array(
            'header'     => Mage::helper('suppliersuccess')->__('Cost') .
                            ' (' . Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE) . ')',
            'name'       => 'cost',
            'type'       => 'input',
            'index'      => 'cost',
            'editable'   => true,
            'edit_only'  => true,
            'filter'     => false,
            'width'      => '80px',
            'inline_css' => 'validate-number validate-zero-or-greater',
        ));

        $this->addColumn('tax', array(
            'header'    => Mage::helper('suppliersuccess')->__('Tax (%)'),
            'name'      => 'tax',
            'type'      => 'input',
            'index'     => 'tax',
            'editable'  => true,
            'edit_only' => true,
            'filter'    => false,
            'width'     => '80px',
            'inline_css' => 'validate-number validate-zero-or-greater',
        ));

    }

    /**
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array(
            '_current' => true,
            'id'       => $this->getRequest()->getParam('id'),
            'store'    => $this->getRequest()->getParam('store'),
        ));
    }

    /**
     *
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getProducts();
        if ( !is_array($products) || $this->importService->getImportProducts() ) {
            $products = array_keys($this->getSelectedRelatedProducts());
        }
        return $products;
    }

    /**
     *
     * @return array
     */
    public function getSelectedRelatedProducts()
    {
        $products          = array();
        $supplier          = $this->getSupplier();
        $productCollection = Mage::getResourceModel('suppliersuccess/supplier_product_collection')
                                 ->addFieldToFilter('supplier_id', $supplier->getId());
        if ( $productCollection->getSize() ) {
            foreach ( $productCollection as $product ) {
                $products[$product->getProductId()] = array(
                    'cost'                 => $product->getCost(),
                    'tax'                  => $product->getTax(),
                    'product_supplier_sku' => $product->getProductSupplierSku(),
                );
            }
        }
        if ( $importProducts = $this->importService->getImportProducts() ) {
            foreach ( $importProducts as $productData ) {
                if ( !isset($productData['id']) ) {
                    continue;
                }
                $products[$productData['id']] = array(
                    'cost'                 => $productData['cost'],
                    'tax'                  => $productData['tax'],
                    'product_supplier_sku' => $productData['product_supplier_sku'],
                );
            }
        }
        return $products;
    }

    /**
     *
     * @return Magestore_Suppliersuccess_Model_Supplier
     */
    public function getSupplier()
    {
        if ( !$this->hasData('supplier') ) {
            $supplier = Mage::getModel('suppliersuccess/supplier')->load($this->getRequest()->getParam('id'));
            $this->setData('supplier', $supplier);
        }
        return $this->getData('supplier');
    }

    /**
     * get currrent store
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }

    /**
     *
     * @return boolean
     */
    public function getRowUrl( $row )
    {
        return false;
    }

    /**
     * prepare layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setChild('import_button',
                        $this->getLayout()->createBlock('coresuccess/adminhtml_widget_button')
                             ->setData(array(
                                           'label'      => Mage::helper('adminhtml')->__('Import products'),
                                           'class'      => 'import-csv',
                                           'attributes' => array(
                                               'data-toggle' => 'modal',
                                               'data-target' => '#import_product',
                                           ),
                                       ))
        );
        return parent::_prepareLayout();
    }

    /**
     * get import button html
     *
     * @return string
     */
    public function getImportButtonHtml()
    {
        return $this->getChildHtml('import_button');
    }

    /**
     * prepare button list
     *
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $html = '';
        if ( $this->getFilterVisibility() ) {
            $html .= $this->getImportButtonHtml();
            $html .= $this->getResetFilterButtonHtml();
            $html .= $this->getSearchButtonHtml();
        }
        return $html;
    }
}