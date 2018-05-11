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
use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Status as PurchaseorderStatus;

class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary
    extends Mage_Adminhtml_Block_Template
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Purchaseorder
     */
    protected $purchaseOrder;

    /**
     * @var string
     */
    protected $_template = 'purchaseordersuccess/purchaseorder/edit/tab/purchase_summary.phtml';

    /**
     * @var Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Grid
     */
    protected $blockGrid;

    /**
     * @var Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Header
     */
    protected $blockHeader;

    /**
     * @var Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Footer
     */
    protected $blockFooter;

    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->purchaseOrder = Mage::registry('current_purchase_order');
    }

    /**
     * Retrieve instance of grid block
     *
     * @return Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_grid
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'purchaseordersuccess/adminhtml_purchaseorder_edit_tab_purchasesummary_grid',
                'purchaseorder.purchasesummary.grid'
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
     * Retrieve instance of grid block
     *
     * @return string
     */
    public function getPurchaseSummaryHeader()
    {
        if (null === $this->blockHeader) {
            $this->blockHeader = $this->getLayout()->createBlock(
                'purchaseordersuccess/adminhtml_purchaseorder_edit_tab_purchasesummary_header',
                'purchaseorder.purchasesummary.header'
            );
        }
        return $this->blockHeader->toHtml();
    }

    /**
     * Retrieve instance of grid block
     *
     * @return string
     */
    public function getPurchaseSummaryFooter()
    {
        if (null === $this->blockFooter) {
            $this->blockFooter = $this->getLayout()->createBlock(
                'purchaseordersuccess/adminhtml_purchaseorder_edit_tab_purchasesummary_footer',
                'purchaseorder.purchasesummary.footer'
            );
        }
        return $this->blockFooter->toHtml();
    }

    /**
     * @return bool
     */
    public function canAddProduct()
    {
        return $this->purchaseOrder->canAddProduct();
    }

    /**
     * @return Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Allsupplierproduct
     */
    public function getAllSupplierProductModal()
    {
        return $this->getLayout()->createBlock(
            'purchaseordersuccess/adminhtml_purchaseorder_edit_tab_purchasesummary_allsupplierproduct',
            'purchaseorder.purchasesummary.allsupplierproduct'
        )->toHtml();
    }

    /**
     * @return Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Lowstockproduct
     */
    public function getLowStockProductModal()
    {
        return $this->getLayout()->createBlock(
            'purchaseordersuccess/adminhtml_purchaseorder_edit_tab_purchasesummary_lowstockproduct',
            'purchaseorder.purchasesummary.lowstockproduct'
        )->toHtml();
    }

    /**
     * @return Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Backsaleproduct
     */
    public function getBackSaleProductModal()
    {
        return $this->getLayout()->createBlock(
            'purchaseordersuccess/adminhtml_purchaseorder_edit_tab_purchasesummary_backsaleproduct',
            'purchaseorder.purchasesummary.lowstockproduct'
        )->toHtml();
    }

    /**
     * @return Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Supplyneedproduct
     */
    public function getSupplyNeedProductModal()
    {
        return $this->getLayout()->createBlock(
            'purchaseordersuccess/adminhtml_purchaseorder_edit_tab_purchasesummary_supplyneedproduct',
            'purchaseorder.purchasesummary.supplyneedproduct'
        )->toHtml();
    }

    /**
     * @return Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Importproduct
     */
    public function getImportProductModal()
    {
        return $this->getLayout()->createBlock(
            'purchaseordersuccess/adminhtml_purchaseorder_edit_tab_purchasesummary_importproduct',
            'purchaseorder.purchasesummary.importproduct'
        )->toHtml();
    }

    /**
     * Check if Magestore_Inventorysuccess is enable
     *
     * @return bool
     */
    public function isInventorySuccessEnable()
    {
        return Mage::helper('purchaseordersuccess')->isModuleEnabled('Magestore_Inventorysuccess');
    }

    /**
     * Check if Mage_CatalogInventory is enable
     *
     * @return bool
     */
    public function isInventoryEnable()
    {
        return Mage::helper('purchaseordersuccess')->isModuleEnabled('Mage_CatalogInventory');
    }

    /**
     * Check if Magestore_Barcodesuccess is enable
     *
     * @return bool
     */
    public function isBarcodeSuccessEnable()
    {
        return Mage::helper('purchaseordersuccess')->isModuleEnabled('Magestore_Barcodesuccess');
    }
}