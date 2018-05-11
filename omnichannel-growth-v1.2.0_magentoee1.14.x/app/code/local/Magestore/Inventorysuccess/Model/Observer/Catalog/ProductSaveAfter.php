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
 * Adjuststock Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Observer_Catalog_ProductSaveAfter
{
    /**
     * 
     * @param type $observer
     */
    public function execute($observer)
    {
        //if import dataflow
        if (Mage::app()->getRequest()->getActionName() == 'batchRun') {
            return;
        }
        
        $product = $observer->getProduct();
        $postData = Mage::app()->getRequest()->getPost();
        Magestore_Coresuccess_Model_Service::productSaveService()->handleProductSaveAfter($product, $postData);
    }
}