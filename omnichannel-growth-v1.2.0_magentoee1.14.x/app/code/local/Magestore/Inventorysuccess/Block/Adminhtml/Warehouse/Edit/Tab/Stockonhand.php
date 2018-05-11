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
class Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Stockonhand extends Mage_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'inventorysuccess/warehouse/edit/tab/stockonhand.phtml';

    /**
     * @var Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Stockonhand_Grid
     */
    protected $blockGrid;

    /**
     * Retrieve instance of grid block
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Stockonhand_Grid
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'inventorysuccess/adminhtml_warehouse_edit_tab_stockonhand_grid',
                'warehouse.stockonhand.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * @return Magestore_Inventorysuccess_Model_Warehouse
     */
    public function getCurrentWarehouse(){
        return Mage::registry('current_warehouse');
    }

    /**
     * Check permission allow delete product in this warehouse
     * 
     * @return bool
     * @throws Exception
     */
    public function canDeleteProduct()
    {
        $warehouse = $this->getCurrentWarehouse();
        return Magestore_Coresuccess_Model_Service::permissionService()->checkPermission(
            'admin/inventorysuccess/stocklisting/warehouse_list/view_warehouse/view_stock_on_hand/delete_product',
            $warehouse
        );
    }
    
    public function canAccessNonWarehouseProduct(){
        return Magestore_Coresuccess_Model_Service::permissionService()->checkPermission(
            'admin/inventorysuccess/stocklisting/non_warehouse_product'
        );
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }
}