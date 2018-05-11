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
use Magestore_Purchaseordersuccess_Model_Return_Options_Status as ReturnStatus;

class Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Transferreditem_Transferitem
    extends Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Abstracttab
{
    /**
     * @var string
     */
    protected $_template = 'purchaseordersuccess/return/edit/tab/transferred_item/transfer_item.phtml';

    /**
     * @var array
     */
    protected $reloadTabs = array('return_request_tabs_transferred_item', 'return_request_tabs_returned_item');

    /**
     * Prepare html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!Mage::helper('purchaseordersuccess')->isModuleEnabled('Magestore_Inventorysuccess'))
            return '';
        $status = $this->returnRequest->getStatus();
        if ($status == ReturnStatus::STATUS_PROCESSING || $status == ReturnStatus::STATUS_COMPLETED)
            return parent::_toHtml();
        else
            return '';
    }

    /**
     * Retrieve instance of grid block
     *
     * @return Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Transferreditem_Transferitem_Grid
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'purchaseordersuccess/adminhtml_return_edit_tab_transferreditem_transferitem_grid',
                'return.transferreditem.transferitem.grid'
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
                'label' => $this->__('Delivery Date'),
                'image' => $this->getSkinUrl('images/grid-cal.gif'),
                'input_format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'format' => Varien_Date::DATE_INTERNAL_FORMAT,
                'required' => true,
                'class' => 'validate-date',
                'min_date' => $this->returnRequest->getReturnedAt(),
                'value' => new Zend_Date($this->returnRequest->getReturnedAt(), Varien_Date::DATE_INTERNAL_FORMAT),
                'readonly' => true
            )
        );
        return $html;
    }

    /**
     * Add Returned Time Field
     *
     * @return Varien_Data_Form_Element_Abstract
     */
    public function addTransferredSubtractField()
    {
        $html = $this->addField('is_subtract',
            'checkbox',
            array(
                'name' => 'is_subtract',
                'label' => $this->__('Subtract stock on warehouse'),
                'checked' => true
            )
        );
        $html .= $this->addField('warehouse_id',
            'hidden',
            array(
                'name' => 'warehouse_id',
                'label' => $this->__(''),
                'value' => $this->returnRequest->getWarehouseId()
            )
        );
        return $html;
    }

    public function getForm()
    {
        if (!$this->form)
            $this->form = $this->getLayout()
                ->createBlock('Magestore_Purchaseordersuccess_Block_Adminhtml_Return_Edit_Tab_Transferreditem_Transferitem_Form');
        return $this->form;
    }

    public function canTransferItem()
    {
        return $this->returnRequest->canTransferItem();
    }
}