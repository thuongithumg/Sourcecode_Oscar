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
 * Stock Transfer Tabs Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_StockMovement_Transfer_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('stock_transfer_tabs');
        $this->setDestElementId('edit_form');
    }

    /**
     * prepare before render block to html
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_LowStockNotification_Rule_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        /** information form */
        $this->addTab('general_information', array(
            'label' => $this->__('General Information'),
            'title' => $this->__('General Information'),
            'content' => $this->getLayout()
                    ->createBlock('inventorysuccess/adminhtml_stockMovement_transfer_edit_tab_general')
                    ->toHtml() .
                $this->getLayout()
                    ->createBlock('inventorysuccess/adminhtml_stockMovement_transfer_edit_tab_items')
                    ->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}