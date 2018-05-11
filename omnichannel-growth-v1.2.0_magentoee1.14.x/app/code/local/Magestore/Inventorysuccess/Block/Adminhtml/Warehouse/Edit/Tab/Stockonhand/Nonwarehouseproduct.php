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
 * Warehouse Edit Stock On Hand Non-warehouse product modal Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Stockonhand_Nonwarehouseproduct extends Mage_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'inventorysuccess/warehouse/edit/tab/stockonhand/nonwarehouseproduct.phtml';

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
                'inventorysuccess/adminhtml_warehouse_edit_tab_stockonhand_nonwarehouseproduct_grid',
                'warehouse.stockonhand.nonwarehouseproduct.grid'
            );
        }
        return $this->blockGrid;
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
    
    public function getJsParentObjectName(){
        return $this->getParentBlock()->getBlockGrid()->getJsObjectName();
    }
}