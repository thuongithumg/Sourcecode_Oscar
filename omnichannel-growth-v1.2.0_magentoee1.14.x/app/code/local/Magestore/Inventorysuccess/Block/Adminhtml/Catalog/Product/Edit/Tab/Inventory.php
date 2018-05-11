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
 * Adjuststock Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Catalog_Product_Edit_Tab_Inventory
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * 
     */
    protected function _prepareForm()
    {
        if(!$this->_isShow()) {
            return;
        }
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('fields', array('legend'=>Mage::helper('catalog')->__('Warehouse Stocks')));
        $form->setDataObject(Mage::getModel('catalog/product'));
        $form->setName('warehouse_stock');
        
        if(!$this->getProduct()->isComposite()) {
            $fieldset->addField('stock_summary', 'label', array(
                'label' => '',
                'name' => 'stock_summary',
            ));   

            $form->getElement('stock_summary')->setRenderer(
                $this->getLayout()->createBlock('inventorysuccess/adminhtml_catalog_product_edit_tab_field_stockSummary')
            );
        }
        
        $fieldset->addField('warehouse_stock', 'text', array(
            'label' => '',
            'name'  => 'warehouse_stock',
        ));
        
        $form->getElement('warehouse_stock')->setRenderer(
            $this->getLayout()->createBlock('inventorysuccess/adminhtml_catalog_product_edit_tab_field_warehouseStock')
        );


        if($this->checkSupplierModule()) {
            $fieldset = $form->addFieldset('fields_supplier', array('legend' => Mage::helper('catalog')->__('Supplier')));
            $fieldset->addField('supplier_stock', 'text', array(
                'label' => '',
                'name' => 'supplier_stock',
            ));
            $form->getElement('supplier_stock')->setRenderer(
                $this->getLayout()->createBlock('inventorysuccess/adminhtml_catalog_product_edit_tab_field_supplierStock')
            );
        }


        if(!$this->getProduct()->isComposite() && $this->getProduct()->getId()) {
            $fieldset = $form->addFieldset('fields_movement', array('legend'=>Mage::helper('catalog')->__('Stocks Movement')));
            $fieldset->addField('stock_movement', 'label', array(
                'label' => '',
                'name' => 'stock_movement',
            ));
            $form->getElement('stock_movement')->setRenderer(
                $this->getLayout()->createBlock('inventorysuccess/adminhtml_catalog_product_edit_tab_field_stockMovement')
            );
        }

        /* fill stock data to warehouse_stock form  */
        $this->_fillStockData($form);
        
        //$form->setFieldNameSuffix('inventorysuccess');
        $this->setForm($form);
    }
    
    /**
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }
    
    /**
     * Retrieve Catalog Inventory  Stock Item Model
     *
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    public function getStockItem()
    {
        return $this->getProduct()->getStockItem();
    }    
    
    /**
     * @return bool
     */
    protected function _isShow()
    {
        if($this->getProduct() && $this->getProduct()->isComposite()) {
            //return false;
        }
        return true;
    }
    
    /**
     * fill stock data to warehouse_stock form 
     * 
     * @param Varien_Data_Form $form
     */
    protected function _fillStockData($form)
    {
        $product = $this->getProduct();
        if($product->getId()) {
            $stockRegistryService = Magestore_Coresuccess_Model_Service::stockRegistryService();
            $stocks = $stockRegistryService->getStocksByProduct($product->getId());
            $warehouseStock = array();
            $stockSummary = array(
                'available_qty' => 0,
                'qty_to_ship' => 0,
                'total_qty' => 0,
            );
            if(count($stocks)) {
                foreach($stocks as $stockData){
                    if(!$stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID]) {
                        continue;
                    }
                    $totalQty = floatval($stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::TOTAL_QTY]);
                    $availableQty = floatval($stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::AVAILABLE_QTY]);
                    $qtyToShip = max($totalQty - $availableQty, 0);
                    $warehouseId = $stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::WAREHOUSE_ID];
                    $warehouseStock[] = array(
                        'warehouse' => $warehouseId,
                        'warehouse_selected_'.$warehouseId => 'selected',
                        'warehouse_disabled' => 'disabled',
                        'available_qty' => $availableQty,
                        'qty_to_ship' => $qtyToShip,
                        'total_qty' => $totalQty,
                        'shelf_location' => $stockData[Magestore_Inventorysuccess_Model_Warehouse_Product::SHELF_LOCATION],
                    );
                    $stockSummary['available_qty'] += $availableQty;
                    $stockSummary['qty_to_ship'] += $qtyToShip;
                    $stockSummary['total_qty'] += $totalQty;
                }
            }
            $form->getElement('warehouse_stock')->setValue($warehouseStock);

            if($this->checkSupplierModule()) {
                $supplierStocks = Magestore_Coresuccess_Model_Service::supplierProductService()->getProductSuppliers($product->getId());
                $supplier_Stock = array();
                if ($supplierStocks->getSize()) {
                    foreach ($supplierStocks as $stock) {
                        $supplier_Stock[] = array(
                            'supplier' => $stock->getSupplierId(),
                            'supplier_selected_' . $stock->getSupplierId() => 'selected',
                            'supplier_disabled' => 'disabled',
                            'product_supplier_sku' => $stock->getProductSupplierSku(),
                            'cost' => $stock->getCost(),
                            'tax' => $stock->getTax(),
                        );
                    }
                }
                $form->getElement('supplier_stock')->setValue($supplier_Stock);
            }

            if($form->getElement('stock_summary')) {
                $form->getElement('stock_summary')->setValue($stockSummary);
            }
        }
    }
    
    /**
     * 
     * @return string
     */
    protected function _toHtml()
    {
        $html = parent::_toHtml();
        return $html;
    }
    
    /**
     * 
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * 
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('InventorySuccess');
    }

    /**
     * 
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('InventorySuccess');
    }

    /**
     * 
     * @return boolean
     */
    public function isHidden()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function checkSupplierModule(){
        $result = false;
        if (Mage::helper('core')->isModuleEnabled('Magestore_Suppliersuccess')) {
            $result = true;
        }
        return $result;
    }

}