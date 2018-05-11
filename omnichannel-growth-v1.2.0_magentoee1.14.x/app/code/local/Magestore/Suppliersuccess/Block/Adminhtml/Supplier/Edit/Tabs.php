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
 * @package     Magestore_Suppliersuccess
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Suppliersuccess Edit Tabs Block
 * 
 * @category    Magestore
 * @package     Magestore_Suppliersuccess
 * @author      Magestore Developer
 */
class Magestore_Suppliersuccess_Block_Adminhtml_Supplier_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('supplier_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('suppliersuccess')->__('Supplier Information'));
    }
    
    /**
     * prepare before render block to html
     *
     * @return Magestore_Suppliersuccess_Block_Adminhtml_Supplier_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('general_section', array(
            'label'     => Mage::helper('suppliersuccess')->__('Supplier Information'),
            'title'     => Mage::helper('suppliersuccess')->__('Supplier Information'),
            'content'   => $this->getLayout()
                                ->createBlock('suppliersuccess/adminhtml_supplier_edit_tab_form')
                                ->toHtml(),
        ));
        
        $this->addTab('address_section', array(
            'label'     => Mage::helper('suppliersuccess')->__('Mailing Address'),
            'title'     => Mage::helper('suppliersuccess')->__('Mailing Address'),
            'content'   => $this->getLayout()
                                ->createBlock('suppliersuccess/adminhtml_supplier_edit_tab_address')
                                ->toHtml(),
        ));
        
        $this->addTab('products_section', array(
            'label' => Mage::helper('suppliersuccess')->__('Product List'),
            'title' => Mage::helper('suppliersuccess')->__('Product List'),
            'url' => $this->getUrl('*/suppliersuccess_product/index', array(
                '_current' => true,
                'id' => $this->getRequest()->getParam('id'),
                'store' => $this->getRequest()->getParam('store')
            )),
            'class' => 'ajax',
        ));      

        if($this->getRequest()->getParam('id')) {
            $this->addTab('pricelist_section', array(
                'label' => Mage::helper('suppliersuccess')->__('Pricelist'),
                'title' => Mage::helper('suppliersuccess')->__('Pricelist'),
                'url' => $this->getUrl('*/suppliersuccess_pricelist/supplier', array(
                    '_current' => true,
                    'id' => $this->getRequest()->getParam('id'),
                    'store' => $this->getRequest()->getParam('store')
                )),
                'class' => 'ajax',
            ));
        }
        
        /**
         * allow to add more tabs by other extensions
         */        
        Mage::dispatchEvent('suppliersuccess_supplier_edit_after_adding_tabs', array('tab' => $this));

        return parent::_beforeToHtml();
    }
}