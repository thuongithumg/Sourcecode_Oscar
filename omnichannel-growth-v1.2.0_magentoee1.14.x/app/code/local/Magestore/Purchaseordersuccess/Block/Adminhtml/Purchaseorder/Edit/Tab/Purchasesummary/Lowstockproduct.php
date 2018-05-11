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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseordersuccess Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Lowstockproduct
    extends Mage_Adminhtml_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'purchaseordersuccess/purchaseorder/edit/tab/purchase_summary/low_stock_product.phtml';

    /**
     * @var Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Lowstockproduct_Grid
     */
    protected $blockGrid;

    /**
     * Retrieve instance of grid block
     *
     * @return Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Lowstockproduct_Grid
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'purchaseordersuccess/adminhtml_purchaseorder_edit_tab_purchasesummary_lowstockproduct_grid',
                'purchaseorder.purchasesummary.lowstockproduct.grid'
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
     * @return string
     */
    public function getJsParentObjectName(){
        return $this->getLayout()
            ->createBlock('purchaseordersuccess/adminhtml_purchaseorder_edit_tab_purchasesummary_grid')
            ->getJsObjectName();
    }

    /**
     * Get Low Stock Product Options
     * 
     * @return array
     */
    public function getLowStockList(){
        return Mage::getModel('purchaseordersuccess/purchaseorder_options_lowStock')
            ->getOptionHash();
    }
}