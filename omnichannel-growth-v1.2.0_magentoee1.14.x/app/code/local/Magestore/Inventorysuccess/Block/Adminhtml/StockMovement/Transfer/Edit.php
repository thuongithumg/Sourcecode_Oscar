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
 * Stock Transfer Edit Block
 *
 * @category     Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_StockMovement_Transfer_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'stock_transfer_id';
        $this->_controller = 'adminhtml_stockMovement_transfer';
        $this->_blockGroup = 'inventorysuccess';

        $this->_removeButton('delete');
        $this->_removeButton('reset');
        $this->_removeButton('save');
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        $stockTransfer = Mage::registry('current_stock_transfer');
        return $this->__("View Receipt/ Delivery History (%s)",
            $this->escapeHtml($stockTransfer->getTransferCode())
        );
    }
}