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
 * Adjuststock Adminhtml Block
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Catalog_Product_Edit_Modal_Orderhistory
    extends Mage_Adminhtml_Block_Template
{
    /**
     * 
     */
    protected function _construct()
    {
        $this->setTemplate('inventorysuccess/catalog/product/edit/modal/order-history.phtml');
        parent::_construct();
    }    
    
    /**
     * 
     * @return string
     */
    public function getOrderHistoryUrl()
    {
        return $this->getUrl('*/inventorysuccess_catalog_product/orderhistorygrid', array(
            '_current' => true,
            'product_id' => $this->getProduct()->getId(),
            'store' => $this->getRequest()->getParam('store')
        ));
    }
    
    /**
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->__('Need-to-ship Items') . ' - ' .
                $this->getProduct()->getName() . ' ('. $this->getProduct()->getSku() .')';
    }
    
    /**
     * 
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('product');
    }    

}