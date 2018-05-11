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
class Magestore_Inventorysuccess_Model_Observer_Catalog_ProductCollectionApplyLimitations
{
    /**
     * 
     * @param type $observer
     */
    public function execute($observer)
    {
        //if adminhtml
        if(Mage::getSingleton('admin/session')->getUser()) {
            return;
        }
        
        $collection = $observer->getEvent()->getCollection();

        Magestore_Coresuccess_Model_Service::productLimitationService()->filterProductByCurrentStock($collection);
    }
}