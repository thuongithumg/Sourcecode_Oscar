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
 * Inventorysuccess Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Nonwarehouseproduct extends Mage_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'inventorysuccess/warehouse/nonwarehouseproduct.phtml';

    /**
     * get hidden input field name for selected products
     *
     * @return string
     */
    public function getHiddenInputField(){
        return 'selected_products';
    }
    
    /**
     * Retrieve instance of grid block
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Stockonhand_Grid
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'inventorysuccess/adminhtml_warehouse_nonwarehouseproduct_grid',
                'warehouses.nonewarehouseproduct.grid'
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
}