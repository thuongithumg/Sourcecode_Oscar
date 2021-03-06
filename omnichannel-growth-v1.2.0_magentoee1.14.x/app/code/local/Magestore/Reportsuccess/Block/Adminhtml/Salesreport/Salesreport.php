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
 * @package     Magestore_Reportsuccess
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 *
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
class Magestore_Reportsuccess_Block_Adminhtml_Salesreport_Salesreport
    extends Mage_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'reportsuccess/salesreport/grid.phtml';

    /**
     * @var Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Stockonhand_Grid
     */
    protected $blockGrid;
    /**
     * @var string
     */
    protected $type= Magestore_Reportsuccess_Helper_Data::SALESREPORT;
    /**
     * @var bool
     */
    protected $canModifiColumn = true;

    /**
     * Retrieve instance of grid block
     *
     * @return Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Stockonhand_Grid
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'reportsuccess/adminhtml_salesreport_grid_grid',
                'report_salesreport_grid.grid'
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
     * @return mixed
     */
    public function getUrlTotalsReport(){
        return  Mage::helper('adminhtml')->getUrl('adminhtml/salesreport_index/getTotals');
    }

    /**
     * @return mixed
     */
    public function getEditcolumnGrid()
    {
        $block = $this->getLayout()->createBlock('reportsuccess/adminhtml_inventoryreport_editcolumn_grid')
        ->setName(Magestore_Reportsuccess_Helper_Data::salesreportGridJsObject);
        $block->setTemplate('reportsuccess/salesreport/customreport.phtml');
        return $block->toHtml();
    }

    /**
     * @return mixed
     */
    public function getUrlToUpdateSalesData(){
        return  Mage::helper('adminhtml')->getUrl('adminhtml/salesreport_index/updateSalesData');
    }


}