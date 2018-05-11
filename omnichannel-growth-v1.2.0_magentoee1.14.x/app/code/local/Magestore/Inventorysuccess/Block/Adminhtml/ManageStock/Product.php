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
 * Inventorysuccess Manage Stock Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_ManageStock_Product extends Mage_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'inventorysuccess/managestock/product.phtml';

    /**
     * @var Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Stockonhand_Grid
     */
    protected $blockGrid;

    /**
     * @var Magestore_Inventorysuccess_Block_Adminhtml_ManageStock_Product_Information
     */
    protected $inforGrid;

    /**
     * @var Magestore_Inventorysuccess_Block_Adminhtml_ManageStock_Product_StockMovement
     */
    protected $movementGrid;

    /**
     * Retrieve instance of grid block
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_ManageStock_Product_Grid
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'inventorysuccess/adminhtml_manageStock_product_grid',
                'managestock.product.grid'
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

    /**
     * Retrieve instance of grid stock information block
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_ManageStock_Product_Information
     */
    public function getStockInformationGrid()
    {
        if (null === $this->inforGrid) {
            $this->inforGrid = $this->getLayout()->createBlock(
                'inventorysuccess/adminhtml_manageStock_product_information',
                'warehouse.product.information'
            );
        }
        return $this->inforGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getStockInformationHtml()
    {
        return $this->getStockInformationGrid()->toHtml();
    }

    /**
     * Retrieve instance of grid stock information block
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_ManageStock_Product_StockMovement
     */
    public function getStockMovementGrid()
    {
        if (null === $this->movementGrid) {
            $this->movementGrid = $this->getLayout()->createBlock(
                'inventorysuccess/adminhtml_manageStock_product_stockMovement',
                'warehouse.product.stockmovement'
            );
        }
        return $this->movementGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getStockMovementHtml()
    {
        return $this->getStockMovementGrid()->toHtml();
    }
}