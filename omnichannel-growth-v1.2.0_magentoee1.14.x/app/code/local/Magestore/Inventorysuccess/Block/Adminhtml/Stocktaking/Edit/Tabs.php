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
 * Stocktaking Edit Tabs Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Edit_Tabs constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('stocktaking_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->getHeaderText());
    }
    
    /**
     * prepare before render block to html
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Edit_Tabs
*/
    protected function _beforeToHtml()
    {
        $stocktakingId = $this->getRequest()->getParam('id');
        if($this->getStocktaking() &&
            $this->getStocktaking()->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_COMPLETED) {
            $this->addTab('information_section', array(
                'label' => Mage::helper('inventorysuccess')->__('Information'),
                'title' => Mage::helper('inventorysuccess')->__('Information'),
                'content' => $this->getLayout()
                        ->createBlock('inventorysuccess/adminhtml_stocktaking_edit_tab_form')
                        ->toHtml(). '</br>'.'</br>'.
                    $this->getLayout()
                        ->createBlock('inventorysuccess/adminhtml_stocktaking_edit_tab_products')
                        ->toHtml(),
            ));
        }else{
            if ($stocktakingId) {
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
                    ->createBlock('inventorysuccess/adminhtml_stocktaking_edit_tab_form')
                    ->toHtml(),
            ));
        }
        return parent::_beforeToHtml();
    }

    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('stocktaking_data')
            && Mage::registry('stocktaking_data')->getId()
        ) {
            return Mage::helper('inventorysuccess')->__("Stocktaking '%s'",
                $this->escapeHtml(Mage::registry('stocktaking_data')->getStocktakingCode())
            );
        }
        return Mage::helper('inventorysuccess')->__('New Stocktaking');
    }

    /**
     * get current stocktaking
     *
     * @return Magestore_Inventory_Model_Stocktaking
     */
    public function getStocktaking()
    {
        if (Mage::registry('stocktaking_data')
            && Mage::registry('stocktaking_data')->getId()
        ) {
            return Mage::registry('stocktaking_data');
        }
        return Mage::getModel('inventorysuccess/stocktaking')->load($this->getRequest()->getParam('id'));
    }
}