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

class Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Returnsummary
    extends Mage_Adminhtml_Block_Template
{
    /**
     * @var Magestore_Purchaseordersuccess_Model_Return
     */
    protected $returnRequest;

    /**
     * @var string
     */
    protected $_template = 'purchaseordersuccess/return/edit/tab/return_summary.phtml';

    /**
     * @var Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Returnsummary_Grid
     */
    protected $blockGrid;

    /**
     * @var Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Returnsummary_Header
     */
    protected $blockHeader;

    /**
     * @var Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Returnsummary_Footer
     */
    protected $blockFooter;

    /**
     * Internal constructor, that is called from real constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->returnRequest = Mage::registry('current_return_request');
    }

    /**
     * Retrieve instance of grid block
     *
     * @return Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Returnsummary_Grid
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'purchaseordersuccess/adminhtml_return_edit_tab_returnsummary_grid',
                'return.returnsummary.grid'
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
    public function getReturnSummaryHeader()
    {
        if (null === $this->blockHeader) {
            $this->blockHeader = $this->getLayout()->createBlock(
                'purchaseordersuccess/adminhtml_return_edit_tab_returnsummary_header',
                'return.returnsummary.header'
            );
        }
        return $this->blockHeader->toHtml();
    }

    /**
     * Retrieve instance of grid block
     *
     * @return string
     */
    public function getReturnSummaryFooter()
    {
        if (null === $this->blockFooter) {
            $this->blockFooter = $this->getLayout()->createBlock(
                'purchaseordersuccess/adminhtml_return_edit_tab_returnsummary_footer',
                'return.returnsummary.footer'
            );
        }
        return $this->blockFooter->toHtml();
    }

    /**
     * @return bool
     */
    public function canAddProduct()
    {
        return $this->returnRequest->canAddProduct();
    }

    /**
     * @return Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Returnsummary_Allsupplierproduct
     */
    public function getAllSupplierProductModal()
    {
        return $this->getLayout()->createBlock(
            'purchaseordersuccess/adminhtml_return_edit_tab_returnsummary_allsupplierproduct',
            'return.returnsummary.allsupplierproduct'
        )->toHtml();
    }

    /**
     * @return Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Returnsummary_Importproduct
     */
    public function getImportProductModal()
    {
        return $this->getLayout()->createBlock(
            'purchaseordersuccess/adminhtml_return_edit_tab_returnsummary_importproduct',
            'return.returnsummary.importproduct'
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