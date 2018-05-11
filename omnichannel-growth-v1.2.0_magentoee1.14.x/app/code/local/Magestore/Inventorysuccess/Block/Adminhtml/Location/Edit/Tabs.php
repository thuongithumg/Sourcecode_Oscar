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
 * Location Edit Tabs Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Location_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Magestore_Inventorysuccess_Block_Adminhtml_Location_Edit_Tabs constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('location_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('inventorysuccess')->__('Mapping Locations - Warehouses'));
    }
    
    /**
     * prepare before render block to html
     *
     * @return Magestore_Inventorysuccess_Block_Adminhtml_Adjuststock_Edit_Tabs
*/
    protected function _beforeToHtml()
    {
        $this->addTab('products_section', array(
            'label' => Mage::helper('inventorysuccess')->__('Mapping'),
            'title' => Mage::helper('inventorysuccess')->__('Mapping'),
            'url' => $this->getUrl('*/*/warehouse', array(
                '_current' => true,
                'id' => $this->getRequest()->getParam('id'),
                'store' => $this->getRequest()->getParam('store')
            )),
            'class' => 'ajax',
            'content' => $this->getLayout()
                ->createBlock('inventorysuccess/adminhtml_location_edit_tab_warehouse')
                ->toHtml()
        ));
        return parent::_beforeToHtml();
    }
}