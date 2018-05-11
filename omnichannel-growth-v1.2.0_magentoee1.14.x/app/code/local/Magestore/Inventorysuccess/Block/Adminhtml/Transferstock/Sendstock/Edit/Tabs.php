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
 * Inventorysuccess Edit Tabs Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Sendstock_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Sendstock_Edit_Tabs constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('sendstock_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('inventorysuccess')->__('Stock Sending Information'));
    }

    /**
     * prepare before render block to html
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Sendstock_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        /** @var Magestore_Inventorysuccess_Model_Transferstock $model */
        $model = Mage::registry('sendstock_data');
        if ($model->getStatus() == Magestore_Inventorysuccess_Model_Transferstock::STATUS_PENDING
            || $model->getStatus() == Magestore_Inventorysuccess_Model_Transferstock::STATUS_CANCELED) {
            $this->addTab('productlist', array(
                'label' => Mage::helper('inventorysuccess')->__('Product List'),
                'title' => Mage::helper('inventorysuccess')->__('Product List'),
                'url' => $this->getUrl('*/*/productlist', array(
                    '_current' => true,
                )),
                'class' => 'ajax',
            ));
        } elseif ($model->getStatus() == Magestore_Inventorysuccess_Model_Transferstock::STATUS_PROCESSING
            || $model->getStatus() == Magestore_Inventorysuccess_Model_Transferstock::STATUS_COMPLETED
        ) {
            $this->addTab('stock_summary', array(
                'label' => Mage::helper('inventorysuccess')->__('Stock Summary'),
                'title' => Mage::helper('inventorysuccess')->__('Stock Summary'),
                'url' => $this->getUrl('*/*/stocksummary', array(
                    '_current' => true,
                )),
                'class' => 'ajax',
            ));
            $this->addTab('receiving_history', array(
                'label' => Mage::helper('inventorysuccess')->__('Receiving History'),
                'title' => Mage::helper('inventorysuccess')->__('Receiving History'),
                'url' => $this->getUrl('*/*/receiving', array(
                    '_current' => true,
                )),
                'class' => 'ajax',
            ));
            $this->addTab('differences', array(
                'label' => Mage::helper('inventorysuccess')->__('Shortfall'),
                'title' => Mage::helper('inventorysuccess')->__('Shortfall'),
                'url' => $this->getUrl('*/*/differences', array(
                    '_current' => true,
                )),
                'class' => 'ajax',
            ));
            $this->addTab('returned', array(
                'label' => Mage::helper('inventorysuccess')->__('Returned'),
                'title' => Mage::helper('inventorysuccess')->__('Returned'),
                'url' => $this->getUrl('*/*/refunded', array(
                    '_current' => true,
                )),
                'class' => 'ajax',
            ));
        }
        $this->addTab('general', array(
            'label' => Mage::helper('inventorysuccess')->__('General Information'),
            'title' => Mage::helper('inventorysuccess')->__('General Information'),
            'content' => $this->getLayout()
                ->createBlock('inventorysuccess/adminhtml_transferstock_sendstock_edit_tab_general')
                ->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
}