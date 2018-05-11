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
 * Warehouse Edit Stock On Hand Tab Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Stockonhand_Grid 
    extends Magestore_Inventorysuccess_Block_Adminhtml_ManageStock_AbstractGridProduct
{
    public function modifyCollection($collection)
    {
        $collection->addWarehouseToFilter($this->getRequest()->getParam('id'));
        return $collection;
    }
    

    /**
     * function to add, remove or modify product grid columns
     *
     * @return $this
     */
    public function modifyColumns()
    {
        $warehouseParam = '';
        if($warehouseId = $this->getRequest()->getParam('id', null)) {
            $warehouseParam = '/warehouse_id/' . $warehouseId;
        }
        $this->addColumn('status',
        array(
            'header'    => $this->__('Status'),
            'align'     =>'left',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        $this->addExportType('*/inventorysuccess_warehouse/exportStockOnHandCsv' . $warehouseParam, $this->__('CSV'));
        $this->addExportType('*/inventorysuccess_warehouse/exportStockOnHandXml' . $warehouseParam, $this->__('Excel XML'));        
        return parent::modifyColumns();
    }    

    /**
     * Grid url getter
     *
     * @deprecated after 1.3.2.3 Use getAbsoluteGridUrl() method instead
     *
     * @return string current grid url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/inventorysuccess_warehouse_stockonhand/grid', array('_current' => true));
    }

    public function getSaveUrl(){
        return $this->getUrl('*/inventorysuccess_warehouse_stockonhand/save', array('_current' => true));
    }
}