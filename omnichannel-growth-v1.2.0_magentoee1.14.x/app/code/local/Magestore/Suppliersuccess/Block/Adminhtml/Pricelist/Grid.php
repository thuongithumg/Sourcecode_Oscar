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
 * Suppliersuccess Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Suppliersuccess
 * @author      Magestore Developer
 */
class Magestore_Suppliersuccess_Block_Adminhtml_Pricelist_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var Magestore_Suppliersuccess_Model_Service_Supplier_SupplierService
     */
    protected $supplierService;
    /**
     * 
     */
    public function __construct()
    {
        parent::__construct();
        $this->supplierService = Magestore_Coresuccess_Model_Service::supplierService();
        $this->setId('pricelistGrid');
        $this->setDefaultSort('supplier_pricelist_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    
    /**
     * 
     * @param type $column
     * @return Magestore_Suppliersuccess_Block_Adminhtml_Supplier_Edit_Tab_Products
     */
    protected function _addColumnFilterToCollection($column)
    {   
        $productIds = $this->_getSelectedProducts();
        if ($column->getId() == 'in_products') {
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('supplier_pricelist_id', array('in' => $productIds));
            }
            return $this;
        }
        return parent::_addColumnFilterToCollection($column);
    }
        
    
    /**
     * prepare collection for block to display
     *
     * @return Magestore_Suppliersuccess_Block_Adminhtml_Suppliersuccess_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('suppliersuccess/supplier_pricelist')->getCollection();
        if($supplierId = $this->getSupplierId()) {
            $collection->addFieldToFilter('supplier_id', $supplierId);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Magestore_Suppliersuccess_Block_Adminhtml_Pricelist_Grid
     */
    protected function _prepareColumns()
    {
        $editable = $this->_isExport ? false : true;
        
        $this->addColumn('in_products', array(
            'header_css_class' => 'a-center',
            'type' => 'checkbox',
            'name' => 'in_products',
            'values' => $this->_getSelectedProducts(),
            'align' => 'center',
            'index' => 'supplier_pricelist_id',
            'use_index' => true,
            'is_system' => true,
        ));
        
        $this->addColumn('supplier_pricelist_id', array(
            'header'    => Mage::helper('suppliersuccess')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'supplier_pricelist_id',
        ));

        $this->addColumn('product_sku', array(
            'header'    => Mage::helper('suppliersuccess')->__('Product SKU'),
            'align'     =>'left',
            'index'     => 'product_sku',
        ));
        
        $this->addColumn('product_name', array(
            'header'    => Mage::helper('suppliersuccess')->__('Product Name'),
            'align'     =>'left',
            'index'     => 'product_name',
        ));
        
        if(!$this->getSupplierId()) {
            $this->addColumn('supplier_id', array(
                'header'    => Mage::helper('suppliersuccess')->__('Supplier'),
                'align'     =>'left',
                'type'      => 'options',
                'index'     => 'supplier_id',
                'options'   => $this->supplierService->getSupplierOption(), 
            ));        
        }
        
        $this->addColumn('minimal_qty', array(
            'header'    => Mage::helper('suppliersuccess')->__('Minimum Qty'),
            'align'     =>'left',
            'type'      =>'number',
            'index'     => 'minimal_qty',
            'editable' => $editable,
            'edit_only' => $editable,            
        ));  
        
        $this->addColumn('cost', array(
            'header'    => Mage::helper('suppliersuccess')->__('Purchase Price') . ' ('.Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE).')',
            'align'     =>'left',
            'type' => 'number',
            'index'     => 'cost',
            'editable' => $editable,
            'edit_only' => $editable,            
        ));        
        
        $this->addColumn('start_date', array(
            'header'    => Mage::helper('suppliersuccess')->__('Start Date'),
            'index'     => 'start_date',
            'type'      => 'datetime',
        ));
        
        $this->addColumn('end_date', array(
            'header'    => Mage::helper('suppliersuccess')->__('End Date'),
            'index'     => 'end_date',
            'type'      => 'datetime',
        ));        
        
        
        $this->addColumn('action',
            array(
                'header'    =>    Mage::helper('suppliersuccess')->__('Action'),
                'width'        => '100',
                'type'        => 'action',
                'getter'    => 'getId',
                'actions'    => array(
                    array(
                        'caption'    => Mage::helper('suppliersuccess')->__('Remove'),
                        'url'        => array('base'=> '*/*/delete'),
                        'field'        => 'id'
                    )),
                'filter'    => false,
                'sortable'    => false,
                'index'        => 'stores',
                'is_system'    => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('suppliersuccess')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('suppliersuccess')->__('XML'));

        return parent::_prepareColumns();
    }
    
    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return null;
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
    
    /**
     * get url for each row in grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('id' => $this->getSupplierId()));
    }    
    
    /**
     * 
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getProducts();
        if (!is_array($products)) {
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
        return array();
    }
    
    /**
     * 
     * @return string
     */
    public function getSupplierId()
    {
        return $this->getRequest()->getParam('id');
    }
  
}