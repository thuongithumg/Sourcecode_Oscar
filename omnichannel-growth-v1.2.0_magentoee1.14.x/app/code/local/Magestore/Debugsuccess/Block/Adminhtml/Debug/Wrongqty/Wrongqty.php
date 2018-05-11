<?php
/**
 * Created by PhpStorm.
 * User: duongdiep
 * Date: 19/02/2017
 * Time: 14:16
 */
class Magestore_Debugsuccess_Block_Adminhtml_Debug_Wrongqty_Wrongqty
    extends Mage_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'debugsuccess/product.phtml';

    /**
     * @var Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Stockonhand_Grid
     */
    protected $blockGrid;

    /**
     * Retrieve instance of grid block
     *
     * @return Magestore_Reportsuccess_Block_Adminhtml_Inventoryreport_Stockonhand_Grid
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'debugsuccess/adminhtml_debug_wrongqty_grid',
                'report_wrongqtyGrids.grid'
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

    public function correctWrongQtyUrl(){
        return  Mage::helper('adminhtml')->getUrl('adminhtml/indp_debug/correctqty');
    }

}