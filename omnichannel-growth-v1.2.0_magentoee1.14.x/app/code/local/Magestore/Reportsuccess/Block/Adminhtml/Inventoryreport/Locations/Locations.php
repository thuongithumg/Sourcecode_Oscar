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
class Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Locations_Locations
    extends Mage_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'reportsuccess/inventoryreport/product.phtml';

    /**
     * @var Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Stockonhand_Grid
     */
    protected $blockGrid;
    /**
     * @var string
     */
    protected $type="locations";
    /**
     * @var bool
     */
    protected $canModifiColumn = false;
    /**
     * Retrieve instance of grid block
     *
     * @return Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Stockonhand_Grid
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'reportsuccess/adminhtml_inventoryreport_locations_grid',
                'report_locationsgrids.grid'
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
    public function getUrlTotalsReport(){
        return  'not-link';
    }



}