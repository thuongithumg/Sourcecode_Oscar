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
 * Adjuststock Edit Tabs Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Adjuststock_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Adjuststock_Edit_Tabs constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('adjuststock_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('inventorysuccess')->__('Stock adjustment Information'));
    }
    
    /**
     * prepare before render block to html
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Adjuststock_Edit_Tabs
*/
    protected function _beforeToHtml()
    {
        $adjustStockId = $this->getRequest()->getParam('id');
        if($this->getCurrentAdjustment() &&
            $this->getCurrentAdjustment()->getStatus() == Magestore_Inventorysuccess_Model_Adjuststock::STATUS_COMPLETED){
            $this->addTab('information_section', array(
                'label' => Mage::helper('inventorysuccess')->__('Information'),
                'title' => Mage::helper('inventorysuccess')->__('Information'),
                'content' => $this->getLayout()
                        ->createBlock('inventorysuccess/adminhtml_adjuststock_edit_tab_form')
                        ->toHtml(). '</br>'.'</br>'.
                    $this->getLayout()
                        ->createBlock('inventorysuccess/adminhtml_adjuststock_edit_tab_products')
                        ->toHtml(),
            ));
        } else {
            if ($adjustStockId) {
                $this->addTab('products_section', array(
                    'label' => Mage::helper('inventorysuccess')->__('Product List'),
                    'title' => Mage::helper('inventorysuccess')->__('Product List'),
                    'url' => $this->getUrl('*/*/product', array(
                        '_current' => true,
                        'id' => $this->getRequest()->getParam('id'),
                        'store' => $this->getRequest()->getParam('store')
                    )),
                    'class' => 'ajax',
                ));
            }
            $this->addTab('form_section', array(
                'label' => Mage::helper('inventorysuccess')->__('General Information'),
                'title' => Mage::helper('inventorysuccess')->__('General Information'),
                'content' => $this->getLayout()
                    ->createBlock('inventorysuccess/adminhtml_adjuststock_edit_tab_form')
                    ->toHtml(),
            ));
        }
        return parent::_beforeToHtml();
    }

    /**
     * get current stock adjustment
     *
     * @return Magestore_Inventorysuccess_Model_Adjuststock
     */
    public function getCurrentAdjustment()
    {
        if (Mage::registry('adjuststock_data')
            && Mage::registry('adjuststock_data')->getId()
        ) {
            return Mage::registry('adjuststock_data');
        }
        return Mage::getModel('inventorysuccess/adjuststock')->load($this->getRequest()->getParam('id'));
    }
}