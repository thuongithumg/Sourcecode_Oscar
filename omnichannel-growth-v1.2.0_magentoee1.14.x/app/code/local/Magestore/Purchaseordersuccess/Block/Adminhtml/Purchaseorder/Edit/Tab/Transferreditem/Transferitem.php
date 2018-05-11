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

class Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Transferreditem_Transferitem
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Abstracttab
{
    /**
     * @var string
     */
    protected $_template = 'purchaseordersuccess/purchaseorder/edit/tab/transferred_item/transfer_item.phtml';

    /**
     * @var array
     */
    protected $reloadTabs = array('purchase_order_tabs_transferred_item', 'purchase_order_tabs_returned_item');

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!Mage::helper('purchaseordersuccess')->isModuleEnabled('Magestore_Inventorysuccess'))
            return '';
        $status = $this->purchaseOrder->getStatus();
        if ($status == PurchaseorderStatus::STATUS_PROCESSING || $status == PurchaseorderStatus::STATUS_COMPLETED)
            return parent::_toHtml();
        else
            return '';
    }

    /**
     * Retrieve instance of grid block
     *
     * @return Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Transferreditem_Transferitem_Grid
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'purchaseordersuccess/adminhtml_purchaseorder_edit_tab_transferreditem_transferitem_grid',
                'purchaseorder.transferreditem.transferitem.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Add Returned Time Field
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function addTransferredTimeField()
    {
        $html = $this->addField('transferred_at',
            'date',
            array(
                'name' => 'transferred_at',
                'time' => false,
                'label' => $this->__('Transfer Date'),
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'required' => true,
                'class' => 'validate-date',
                'min_date' => $this->purchaseOrder->getPurchasedAt(),
                'value' => new Zend_Date($this->purchaseOrder->getPurchasedAt(), Varien_Date::DATE_INTERNAL_FORMAT),
                'readonly' => true
            )
        );
        $html .= $this->addField('warehouse_id',
            'select',
            array(
                'name' => 'warehouse_id',
                'time' => false,
                'label' => $this->__('Warehouse'),
                'options' => Mage::getModel('purchaseordersuccess/purchaseorder_options_warehouse')->getOptionHash(),
                'required' => true
            )
        );
        return $html;
    }

    public function getForm()
    {
        if (!$this->form)
            $this->form = $this->getLayout()
                ->createBlock('Magestore_Purchaseordersuccess_Block_Adminhtml_Purchaseorder_Edit_Tab_Transferreditem_Transferitem_Form');
        return $this->form;
    }

    public function canTransferItem()
    {
        return $this->purchaseOrder->canTransferItem();
    }
}