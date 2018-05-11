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
 * Class Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Edit_Tab_Products
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Edit_Tab_Products extends
    Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var
     */
    protected $_selectedProducts;

    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Edit_Tab_Products constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $productIds = $this->_getSelectedProducts();
        $this->setId('stocktakingproductGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setDefaultFilter(array('in_products' => 1));
        if ( !$productIds ) {
            $this->setDefaultFilter(array('in_products' => 0));
        }
    }

    /**
     * prepare column to filter
     *
     * @param $column
     * @return $this
     */
    protected function _addColumnFilterToCollection( $column )
    {
        if ( $column->getId() == 'in_products' ) {
            $productIds = $this->_getSelectedProducts();
            if ( empty($productIds) ) {
                $productIds = 0;
            }
            if ( $column->getFilter()->getValue() && $productIds ) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } elseif ( !empty($productIds) ) {
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
        $collection        = Mage::getModel('inventorysuccess/stocktaking_product')->getCollection();
        $stocktakingId     = $this->getRequest()->getParam('id');
        $productCollection = $collection->getProductsToStocktake($stocktakingId);
        if ( $storeId = $this->getRequest()->getParam('store', 0) ) {
            $productCollection->addStoreFilter($storeId);
        }
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
        $stocktaking = $this->getStocktaking();
        if ( $stocktaking->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PENDING ) {
            $this->setChild('import_button',
                            $this->getLayout()->createBlock('inventorysuccess/adminhtml_widget_button')
                                 ->setData(array(
                                               'label'      => Mage::helper('adminhtml')->__('Import products'),
                                               'class'      => 'import-csv',
                                               'attributes' => array(
                                                   'data-toggle' => 'modal',
                                                   'data-target' => '#import_product',
                                               ),
                                           ))
            );

            $this->setChild('export_button',
                            $this->getLayout()->createBlock('adminhtml/widget_button')
                                 ->setData(array(
                                               'label'   => Mage::helper('adminhtml')->__('Export'),
                                               'onclick' => $this->getJsObjectName() . '.doExport()',
                                               'class'   => 'task',
                                           ))
            );
        }
        if ( $stocktaking->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PROCESSING ) {
            $this->setChild('import_button',
                            $this->getLayout()->createBlock('inventorysuccess/adminhtml_widget_button')
                                 ->setData(array(
                                               'label'      => Mage::helper('adminhtml')->__('Import products to count'),
                                               'class'      => 'import-csv',
                                               'attributes' => array(
                                                   'data-toggle' => 'modal',
                                                   'data-target' => '#import_product',
                                               ),
                                           ))
            );
        }
        if ( $stocktaking->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_VERIFIED ||
             $stocktaking->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_COMPLETED
        ) {
            $this->setChild('export_button',
                            $this->getLayout()->createBlock('adminhtml/widget_button')
                                 ->setData(array(
                                               'label'   => Mage::helper('adminhtml')->__('Export counted products'),
                                               'onclick' => $this->getJsObjectName() . '.doExport()',
                                               'class'   => 'task',
                                           ))
            );
        }

        $this->setChild('reset_filter_button',
                        $this->getLayout()->createBlock('adminhtml/widget_button')
                             ->setData(array(
                                           'label'   => Mage::helper('adminhtml')->__('Reset Filter'),
                                           'onclick' => $this->getJsObjectName() . '.resetFilter()',
                                       ))
        );
        $this->setChild('search_button',
                        $this->getLayout()->createBlock('adminhtml/widget_button')
                             ->setData(array(
                                           'label'   => Mage::helper('adminhtml')->__('Search'),
                                           'onclick' => $this->getJsObjectName() . '.doFilter()',
                                           'class'   => 'task',
                                       ))
        );
        $handleBarcodeUrl = Mage::helper('adminhtml')->getUrl(
            'adminhtml/inventorysuccess_stocktaking/handleBarcode',
            array('stocktaking_id' => $this->getRequest()->getParam('id'))
        );
        $this->setChild('scanbarcode_button',
                        $this->getLayout()
                             ->createBlock('adminhtml/widget_button')
                             ->setData(array(
                                           'label'   => Mage::helper('adminhtml')->__('Scan Barcode'),
                                           'onclick' => "jQuery('#scan-barcode').modal();scanBarcode.handleUrl='$handleBarcodeUrl';",
                                           'class'   => '',
                                       ))
        );
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
        $stockTaking = $this->getStocktaking();
        if ( Magestore_Inventorysuccess_Helper_Data::isBarcodeInstalled()
             && ($stockTaking->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PENDING
                 || $stockTaking->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PROCESSING)
        ) {
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
        $stocktaking = $this->getStocktaking();
        $html        = $this->getScanBarcodeButtonHtml();
        if ( $this->getFilterVisibility() ) {
            $html .= $this->getImportButtonHtml();
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
        $model = $this->getStocktaking();
        if ( $model->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PENDING ||
             $model->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PROCESSING
        ) {
            $this->addColumn('in_products', array(
                'header_css_class' => 'a-center',
                'type'             => 'checkbox',
                'name'             => 'in_products',
                'values'           => $this->_getSelectedProducts(),
                'align'            => 'center',
                'index'            => 'entity_id',
                'is_system'        => true,
            ));


        }

        if ( $model->getStatus() != Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PROCESSING ) {
            $this->addColumn('entity_id', array(
                'header'    => Mage::helper('catalog')->__('ID'),
                'sortable'  => true,
                'width'     => '60px',
                'index'     => 'entity_id',
                'is_system' => true,
            ));
        }

        if ( $model->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PROCESSING ) {
            $this->addColumn('stocktaking_qty', array(
                'header'     => Mage::helper('inventorysuccess')->__('Counted Qty'),
                'name'       => 'stocktaking_qty',
                'type'       => 'input',
                'index'      => 'stocktaking_qty',
                'width'      => '80px',
                'editable'   => true,
                'edit_only'  => true,
                'filter'     => false,
                'is_system'  => true,
                'inline_css' => 'validate-number validate-zero-or-greater required-entry',
            ));

            $this->addColumn('stocktaking_reason', array(
                'header'     => Mage::helper('inventorysuccess')->__('Reason of discrepancy'),
                'name'       => 'stocktaking_reason',
                'type'       => 'input',
                'index'      => 'stocktaking_reason',
                'width'      => '80px',
                'editable'   => true,
                'edit_only'  => true,
                'filter'     => false,
                'is_system'  => true,
                'renderer' => 'inventorysuccess/adminhtml_manageStock_grid_column_renderer_input',
            ));
        }

        $this->addColumn('product_name_label', array(
            'header' => Mage::helper('catalog')->__('Name'),
            'align'  => 'left',
            'index'  => 'name',
            'width'  => '550',
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

        $this->addColumn('product_sku_label', array(
            'header' => Mage::helper('catalog')->__('SKU'),
            'width'  => '80px',
            'index'  => 'sku',
        ));

        $this->addColumn('product_sku', array(
            'header'           => Mage::helper('inventorysuccess')->__(' '),
            'type'             => 'input',
            'index'            => 'sku',
            'name'             => 'product_sku',
            'width'            => '80px',
            'column_css_class' => 'no-display',
            'header_css_class' => 'no-display',
            'editable'         => true,
            'is_system'        => true,
        ));

        if ( $model->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PENDING ) {
            $this->addColumn('stocktaking_qty', array(
                'header'           => Mage::helper('inventorysuccess')->__('Counted Qty'),
                'type'             => 'text',
                'index'            => 'stocktaking_qty',
                'width'            => '80px',
                'default'          => '0',
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display',
            ));
        }

        if ( $model->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PROCESSING ) {
            $this->addColumn('entity_id', array(
                'header'    => Mage::helper('catalog')->__('ID'),
                'sortable'  => true,
                'width'     => '60px',
                'index'     => 'entity_id',
                'align'     => 'right',
                'is_system' => true,
            ));
        }

        if ( $model->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PENDING ||
             $model->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_VERIFIED ||
             $model->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_COMPLETED
        ) {
            $this->addExportType('*/*/exportProductCsv', Mage::helper('inventorysuccess')->__('CSV'));
            $this->addExportType('*/*/exportProductXml', Mage::helper('inventorysuccess')->__('XML'));
        }

        if ( $model->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_VERIFIED ) {
            $this->addColumn('stocktaking_qty', array(
                'header'  => Mage::helper('inventorysuccess')->__('Counted Qty'),
                'type'    => 'number',
                'index'   => 'stocktaking_qty',
                'width'   => '80px',
                'default' => '0',
            ));

            $this->addColumn('stocktaking_reason', array(
                'header'  => Mage::helper('inventorysuccess')->__('Reason of discrepancy'),
                'index'   => 'stocktaking_reason',
                'width'   => '80px',
            ));
        }

        if ( $model->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_COMPLETED ) {
            $this->addColumn('old_qty_label', array(
                'header'   => Mage::helper('inventorysuccess')->__('Old Qty'),
                'width'    => '80px',
                'type'     => 'number',
                'index'    => 'old_qty',
                'name'     => 'old_qty',
                'default'  => '0',
                'editable' => false,
            ));

            $this->addColumn('old_qty', array(
                'header'           => Mage::helper('inventorysuccess')->__(' '),
                'type'             => 'input',
                'index'            => 'qty',
                'width'            => '500px',
                'name'             => 'old_qty',
                'column_css_class' => 'no-display',
                'header_css_class' => 'no-display',
                'editable'         => true,
                'is_system'        => true,
            ));
            $this->addColumn('stocktaking_qty', array(
                'header'  => Mage::helper('inventorysuccess')->__('Counted Qty'),
                'type'    => 'number',
                'index'   => 'stocktaking_qty',
                'width'   => '80px',
                'default' => '0',
            ));

            $this->addColumn('stocktaking_reason', array(
                'header'  => Mage::helper('inventorysuccess')->__('Reason of discrepancy'),
                'index'   => 'stocktaking_reason',
                'width'   => '80px',
            ));

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
            'id'       => $this->getRequest()->getParam('id'),
            'store'    => $this->getRequest()->getParam('store'),
        ));
    }

    /**
     * get row url
     *
     * @param $row
     * @return bool
     */
    public function getRowUrl( $row )
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
        $id                  = $this->getRequest()->getParam('id');
        $productIds          = array();
        $stocktakingProducts = Mage::getModel('inventorysuccess/stocktaking_product')
                                   ->getCollection()
                                   ->addFieldToFilter('stocktaking_id', $id);
        foreach ( $stocktakingProducts as $stocktakingProduct ) {
            $productIds[] = $stocktakingProduct->getProductId();
        }
        $collection = Mage::getModel('catalog/product')->getCollection()
                          ->addAttributeToSelect('*')
                          ->addFieldToFilter('entity_id', array('in' => $productIds));
        $collection->joinField('old_qty', 'inventorysuccess/stocktaking_product', 'old_qty', 'product_id=entity_id', '{{table}}.stocktaking_id=' . $id, 'left');
        $collection->joinField('stocktaking_qty', 'inventorysuccess/stocktaking_product', 'stocktaking_qty', 'product_id=entity_id', '{{table}}.stocktaking_id=' . $id, 'left');
        if ( $storeId = $this->getRequest()->getParam('store', 0) ) {
            $collection->addStoreFilter($storeId);
        }
        $collection->addOrder('entity_id', 'ASC');
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

        if ( !$this->_selectedProducts ) {
            if ( $this->getRequest()->getParam('id') ) {
                $collection = $this->getProductSelect();
                if ( $collection ) {
                    foreach ( $collection as $product ) {
                        $products[$product->getId()] = array(
                            'stocktaking_qty' => $product->getData('stocktaking_qty') + 0,
                            'product_sku'     => $product->getData('sku'),
                            'product_name'    => $product->getData('name'),
                            'old_qty'         => $product->getData('qty'),
                        );
                    }
                }
            } elseif ( Mage::getSingleton('adminhtml/session')->getData('stocktaking_products') ) {
                $products = Mage::getSingleton('adminhtml/session')->getData('stocktaking_products');
            }
            $this->_selectedProducts = $products;
        }
        return $this->_selectedProducts;
    }

    /**
     * @param $productId
     * @return mixed
     */
    protected function getOldQty( $productId )
    {
        $warehouseId           = $this->getStocktaking()->getWarehouseId();
        $warehouseStockService = Magestore_Coresuccess_Model_Service::warehouseStockService();
        $warehouseStock        = $warehouseStockService->getStocks($warehouseId, $productId)->getFirstItem();
        return $warehouseStock->getTotalQty();
    }

    /**
     * get current stocktaking
     *
     * @return Magestore_Inventorysuccess_Model_Stocktaking
     */
    public function getStocktaking()
    {
        if ( Mage::registry('stocktaking_data')
             && Mage::registry('stocktaking_data')->getId()
        ) {
            return Mage::registry('stocktaking_data');
        }
        return Mage::getModel('inventorysuccess/stocktaking')->load($this->getRequest()->getParam('id'));
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
