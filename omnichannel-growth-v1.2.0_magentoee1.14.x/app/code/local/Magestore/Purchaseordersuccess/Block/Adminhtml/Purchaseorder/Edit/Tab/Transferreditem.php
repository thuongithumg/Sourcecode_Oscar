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
class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Transferreditem
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Abstracttab
{
    /**
     * @var string
     */
    protected $_template = 'purchaseordersuccess/purchaseorder/edit/tab/transferred_item.phtml';

    /**
     * Retrieve instance of grid block
     *
     * @return Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Transferreditem_Grid
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'purchaseordersuccess/adminhtml_purchaseorder_edit_tab_transferreditem_grid',
                'purchaseorder.transferreditem.grid'
            );
        }
        return $this->blockGrid;
    }

    public function canTransferItem()
    {
        return $this->purchaseOrder->canTransferItem();
    }
    
    /**
     * @var array
     */
    protected $reloadTabs = array(
        'purchase_order_tabs_transferred_item',
        'purchase_order_tabs_returned_item',
    );
}