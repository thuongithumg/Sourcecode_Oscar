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
 * Class Magestore_Inventorysuccess_Block_Adminhtml_Adjuststock_Edit_Tab_Products
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Adjuststock_Edit_Tab_Products extends
    Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var
     */
    protected $_selectedProducts;

    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Adjuststock_Edit_Tab_Products constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $productIds = $this->_getSelectedProducts();
        $this->setId('adjuststockproductGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setDefaultFilter(array('in_products' => 1));
        if (!$productIds) {
            $this->setDefaultFilter(array('in_products' => 0));
        }
    }

    /**
     * prepare column to filter
     *
     * @param $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue() && $productIds) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } elseif (!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }

    /**
     * prepare product collection
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('inventorysuccess/adjuststock_product')->getCollection();
        $adjustStockId = $this->getRequest()->getParam('id');
        $productCollection = $collection->getProductsToAdjust($adjustStockId);
        if ($storeId = $this->getRequest()->getParam('store', 0)) {
            $collection->addStoreFilter($storeId);
        }
        Mage::getSingleton('inventorysuccess/service_filterCollection_filter')->mappingAttribute($productCollection,'at_old_qty.product_id');
        $this->setCollection($productCollection);
        return parent::_prepareCollection();
    }

    /**
     * prepare layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setChild('import_button',
            $this->getLayout()->createBlock('inventorysuccess/adminhtml_widget_button')
                ->setData(array(
                    'label' => Mage::helper('adminhtml')->__('Import products'),
                    'class' => 'import-csv',
                    'attributes' => array(
                        'data-toggle' => 'modal',
                        'data-target' => '#import_product',
                    ),
                ))
        );

        $handleBarcodeUrl = Mage::helper('adminhtml')->getUrl(
            'adminhtml/inventorysuccess_adjuststock/handleBarcode',
            array('adjuststock_id' => $this->getAdjustStock()->getId())
        );
        $this->setChild('scanbarcode_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('adminhtml')->__('Scan Barcode'),
                    'onclick' => "jQuery('#scan-barcode').modal();scanBarcode.handleUrl='$handleBarcodeUrl';",
                    'class' => '',
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
     * @return string
     */
    protected function getScanBarcodeButtonHtml()
    {
        if (Magestore_Inventorysuccess_Helper_Data::isBarcodeInstalled()) {
            return $this->getChildHtml('scanbarcode_button');
        }
        return '';
    }

    /**
     * prepare button list
     *
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $adjustStock = $this->getAdjustStock();
        $html = $this->getScanBarcodeButtonHtml();
        if ($this->getFilterVisibility()) {
            if ($adjustStock->getStatus() == Magestore_Inventorysuccess_Model_Adjuststock::STATUS_PENDING) {
                $html .= $this->getImportButtonHtml();
            }
            $html .= $this->getResetFilterButtonHtml();
            $html .= $this->getSearchButtonHtml();
        }
        return $html;
    }

    /**
     * prepare columns
     */
    protected function _prepareColumns()
    {
        $adjustStock = $this->getAdjustStock();
        $helper = Mage::helper('inventorysuccess');
        if ($adjustStock->getStatus() == Magestore_Inventorysuccess_Model_Adjuststock::STATUS_PENDING) {
            $this->addColumn('in_products', array(
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_products',
                'values' => $this->_getSelectedProducts(),
                'align' => 'center',
                'index' => 'entity_id',
            ));
            if ($helper->getAdjustStockChange()) {
                $this->addColumn('change_qty', array(
                    'header' => Mage::helper('inventorysuccess')->__('Change Qty'),
                    'name' => 'change_qty',
                    'type' => 'input',
                    'index' => 'change_qty',
                    'width' => '80px',
                    'editable' => true,
                    'edit_only' => true,
                    'filter' => false,
                    'inline_css' => 'validate-number required-entry',
                ));
            }
            if (!$helper->getAdjustStockChange()) {
                $this->addColumn('adjust_qty', array(
                    'header' => Mage::helper('inventorysuccess')->__('Adjust Qty'),
                    'name' => 'adjust_qty',
                    'type' => 'input',
                    'index' => 'adjust_qty',
                    'width' => '80px',
                    'editable' => true,
                    'edit_only' => true,
                    'filter' => false,
                    'inline_css' => 'validate-number validate-zero-or-greater required-entry',
                ));
            }

            $this->addColumn('qty', array(
                'header' => Mage::helper('inventorysuccess')->__('Current Qty'),
                'width' => '80px',
                'type' => 'number',
                'index' => 'old_qty',
                'name' => 'old_qty',
                'default' => '0',
            ));

            $this->addColumn('old_qty', array(
                'header' => Mage::helper('inventorysuccess')->__(' '),
                'type' => 'input',
                'index' => 'qty',
                'width' => '550px',
                'name' => 'old_qty',
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display',
                'editable' => true,
            ));
        }

        if ($adjustStock->getStatus() != Magestore_Inventorysuccess_Model_Adjuststock::STATUS_PENDING) {
            $this->addColumn('entity_id', array(
                'header' => Mage::helper('catalog')->__('ID'),
                'sortable' => true,
                'width' => '60px',
                'index' => 'entity_id',
                'is_system' => true,
            ));
        }

        $this->addColumn('product_name_label', array(
            'header' => Mage::helper('catalog')->__('Name'),
            'align' => 'left',
            'index' => 'name',
        ));

        $this->addColumn('product_name', array(
            'header' => Mage::helper('inventorysuccess')->__(' '),
            'type' => 'input',
            'index' => 'name',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
            'editable' => true,
            'is_system' => true,

        ));

        $this->addColumn('product_sku_label', array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'width' => '150px',
            'index' => 'sku',
            'name' => 'sku',
        ));

        $this->addColumn('product_sku', array(
            'header' => Mage::helper('inventorysuccess')->__(' '),
            'type' => 'input',
            'index' => 'sku',
            'name' => 'product_sku',
            'width' => '60px',
            'column_css_class'=>'no-display',
            'header_css_class'=>'no-display' ,
            'editable' => true,
            'is_system'   => true
        ));

        if ($adjustStock->getStatus() == Magestore_Inventorysuccess_Model_Adjuststock::STATUS_PENDING) {
            $this->addColumn('entity_id', array(
                'header' => Mage::helper('catalog')->__('ID'),
                'sortable' => true,
                'width' => '60px',
                'index' => 'entity_id',
                'align' => 'right',
            ));
        }

        if ($adjustStock->getStatus() != Magestore_Inventorysuccess_Model_Adjuststock::STATUS_PENDING) {
            $this->addColumn('old_qty_label', array(
                'header' => Mage::helper('inventorysuccess')->__('Old Qty'),
                'width' => '80px',
                'type' => 'number',
                'index' => 'old_qty',
                'name' => 'old_qty',
                'default' => '0',
                'editable' => false,
            ));

            $this->addColumn('old_qty', array(
                'header' => Mage::helper('inventorysuccess')->__(' '),
                'type' => 'input',
                'index' => 'qty',
                'width' => '500px',
                'name' => 'old_qty',
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display',
                'editable' => true,
                'is_system' => true,
            ));
            if ($helper->getAdjustStockChange()) {
                $this->addColumn('change_qty', array(
                    'header' => Mage::helper('inventorysuccess')->__('Change Qty'),
                    'type' => 'number',
                    'index' => 'change_qty',
                    'width' => '80px',
                    'default' => '0',
                ));
            }
            $this->addColumn('adjust_qty', array(
                'header' => Mage::helper('inventorysuccess')->__('Adjust Qty'),
                'type' => 'number',
                'index' => 'adjust_qty',
                'width' => '80px',
                'default' => '0',
            ));

            $this->addExportType('*/*/exportProductCsv', Mage::helper('inventorysuccess')->__('CSV'));
            $this->addExportType('*/*/exportProductXml', Mage::helper('inventorysuccess')->__('XML'));
        }

        $this->addColumn('status',
            array(
                'header'    => $this->__('Status'),
                'align'     =>'left',
                'index'     => 'status',
                'type'      => 'options',
                'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray(),
            ));

    }

    /**
     * get grid ajax url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/productGrid', array(
            '_current' => true,
            'id' => $this->getRequest()->getParam('id'),
            'store' => $this->getRequest()->getParam('store'),
        ));
    }

    /**
     * get row url
     *
     * @param $row
     * @return bool
     */
    public function getRowUrl($row)
    {
        return false;
    }

    /**
     * prepare selected product for filter
     *
     * @return array|string
     */
    protected function _getSelectedProducts()
    {
        if ($products = $this->getRequest()->getParam('products'))
            return $products;
        $productArrays = $this->getProductSelect();
        if (count($productArrays)) {
            return $productArrays->getColumnValues('entity_id');
        } else {
            return array_keys($this->getSelectedRelatedProducts());
        }
    }

    /**
     * get selected product collection for filter
     *
     * @return mixed
     */
    public function getProductSelect()
    {
        $adjustStockId = $this->getRequest()->getParam('id');
        $storeId = $this->getRequest()->getParam('store', 0);
        $productCollection = Mage::getModel('inventorysuccess/adjuststock_product')->getCollection();
        $collection = $productCollection->getProductSelect($adjustStockId, $storeId);
        return $collection;
    }

    /**
     * get all selected product to params
     *
     * @return array
     */
    public function getSelectedRelatedProducts()
    {
        $products = array();
        $helper = Mage::helper('inventorysuccess');
        if (!$this->_selectedProducts) {
            if ($this->getRequest()->getParam('id')) {
                $collection = $this->getProductSelect();
                if ($collection->getSize()) {
                    if ($helper->getAdjustStockChange()) {
                        foreach ($collection as $product) {
                            $adjustQty = $product->getData('change_qty') + $product->getData('old_qty');
                            $products[$product->getId()] = array(
                                'adjust_qty' => $adjustQty,
                                'product_sku' => $product->getData('sku'),
                                'product_name' => $product->getData('name'),
                                'old_qty' => $product->getData('qty'),
                                'change_qty' => $product->getData('change_qty') + 0,
                            );
                        }
                    } else {
                        foreach ($collection as $product) {
                            $changeQty = $product->getData('adjust_qty') + $product->getData('old_qty');
                            $products[$product->getId()] = array(
                                'adjust_qty' => $product->getData('adjust_qty') + 0,
                                'product_sku' => $product->getData('sku'),
                                'product_name' => $product->getData('name'),
                                'old_qty' => $product->getData('qty'),
                                'change_qty' => $changeQty,
                            );
                        }
                    }
                }

            } elseif (Mage::getSingleton('adminhtml/session')->getData('adjuststock_products')) {
                $products = Mage::getSingleton('adminhtml/session')->getData('adjuststock_products');
            }
            $this->_selectedProducts = $products;
        }
        return $this->_selectedProducts;
    }

    /**
     * @param $products
     * @return mixed
     */
    protected function addProductFromBarcode($products)
    {
        $barcodes = Mage::getSingleton('adminhtml/session')->getData('scan_barcodes', true);
        foreach ($barcodes as $barcode) {
            if (array_key_exists($barcode['product_id'], $products)) {
                $products[$barcode['product_id']]['change_qty'] += $barcode['qty'];
                $products[$barcode['product_id']]['adjust_qty'] += $barcode['qty'];
            } else {
                $oldQty = $this->getOldQty($barcode['product_id']);
                if (Mage::helper('inventorysuccess')->getAdjustStockChange()) {
                    $products[$barcode['product_id']] = array(
                        'adjust_qty' => $oldQty + $barcode['qty'],
                        'product_name' => $barcode['product_name'],
                        'product_sku' => $barcode['product_sku'],
                        'old_qty' => $oldQty,
                        'change_qty' => $barcode['qty'],
                    );
                } else {
                    $products[$barcode['product_id']] = array(
                        'adjust_qty' => $barcode['qty'],
                        'product_name' => $barcode['product_name'],
                        'product_sku' => $barcode['product_sku'],
                        'old_qty' => $oldQty,
                        'change_qty' => $oldQty + $barcode['qty'],
                    );
                }
            }
        }
        return $products;
    }

    /**
     * @param $productId
     * @return mixed
     */
    protected function getOldQty($productId)
    {
        $warehouseId = $this->getAdjustStock()->getWarehouseId();
        $warehouseStockService = Magestore_Coresuccess_Model_Service::warehouseStockService();
        $warehouseStock = $warehouseStockService->getStocks($warehouseId, $productId)->getFirstItem();
        return $warehouseStock->getTotalQty();
    }

    /**
     * get current adjust stock
     *
     * @return Magestore_Inventorysuccess_Model_Adjuststock
     */
    public function getAdjustStock()
    {
        if (!$this->_adjustStock) {
            $this->_adjustStock = Mage::getModel('inventorysuccess/adjuststock')->load($this->getRequest()->getParam('id'));
        }
        return $this->_adjustStock;
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

}
