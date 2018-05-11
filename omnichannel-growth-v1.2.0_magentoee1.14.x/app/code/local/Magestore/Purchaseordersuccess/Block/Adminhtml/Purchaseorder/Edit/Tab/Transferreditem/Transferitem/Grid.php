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
class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Transferreditem_Transferitem_Grid
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Purchasesummary_Abstractmodalgrid
{
    /**
     * Grid ID
     *
     * @var string
     */
    protected $gridId = 'purchase_order_transferred_item_transfer_item_list';

    /**
     * @var string
     */
    protected $hiddenInputField = 'selected_transfer_item';

    /**
     * @var array
     */
    protected $editFields = array('transfer_qty');

    protected $availableField = 'main_table.qty_received - main_table.qty_returned - main_table.qty_transferred';

    /**
     * @return Magestore_Purchaseordersuccess_Model_Mysql4_Purchaseorder_Item_Collection
     */
    protected function getDataColllection()
    {
        $collection = $this->purchaseOrder->getItems();
        if (!$this->getRequest()->getParam('supplier_id'))
            $collection->addFieldToFilter('product_id', 0);
        else
            $collection->getSelect()
                ->columns(array(
                    'available_qty' => new \Zend_Db_Expr('main_table.qty_received - main_table.qty_returned - main_table.qty_transferred')
                ))
                ->where(new \Zend_Db_Expr('main_table.qty_received - (main_table.qty_returned + main_table.qty_transferred)') . ' > 0');

        return $collection;
    }

    /**
     * Modify modal grid columns
     *
     * @return $this
     */
    protected function modifyColumn()
    {
        $this->removeColumn('current_cost');
        $this->removeColumn('cost');
        $this->addColumn("qty_received",
            array(
                "header" => $this->__("Received Qty"),
                "index" => "qty_received",
                'type' => 'number',
            )
        );
        $this->addColumn("qty_returned",
            array(
                "header" => $this->__("Returned Qty"),
                "index" => "qty_returned",
                'type' => 'number',
            )
        );
        $this->addColumn("qty_transferred",
            array(
                "header" => $this->__("Transferred Qty"),
                "index" => "qty_transferred",
                'type' => 'number',
            )
        );
        $this->addColumn("available_qty",
            array(
                "header" => $this->__("Remaining Qty"),
                "index" => "available_qty",
                'type' => 'number',
                'filter_condition_callback' => array($this, '_filterAvailableQtyCallback')
            )
        );
        $this->addColumn("transfer_qty",
            array(
                "header" => $this->__("Transfer Qty"),
                'type' => 'number',
                'sortable' => false,
                'filter' => false,
                'renderer' => 'purchaseordersuccess/adminhtml_grid_column_renderer_text',
                "index" => "available_qty",
                'editable' => true,
                'edit_only' => true
            )
        );
        $this->_exportTypes = array();
        return $this;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            "*/purchaseordersuccess_purchaseorder_transferreditem/gridmodal",
            array("_current" => true, 'supplier_id' => $this->purchaseOrder->getSupplierId())
        );
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl(
            '*/purchaseordersuccess_purchaseorder_transferreditem/transfer',
            array("_current" => true, 'supplier_id' => $this->purchaseOrder->getSupplierId())
        );
    }
}