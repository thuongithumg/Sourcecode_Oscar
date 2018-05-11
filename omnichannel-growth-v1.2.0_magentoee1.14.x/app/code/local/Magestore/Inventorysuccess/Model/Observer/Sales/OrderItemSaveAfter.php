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
class Magestore_Inventorysuccess_Model_Observer_Sales_OrderItemSaveAfter
{
    /**
     * 
     * @param type $observer
     */
    public function execute($observer)
    {
        $item = $observer->getEvent()->getItem();
        $key = Magestore_Inventorysuccess_Model_Service_OrderProcess_PlaceNewOrderService::REGISTRY_ITEM_KEY . $item->getId();
        $itemBefore = Mage::registry(Magestore_Inventorysuccess_Model_Service_OrderProcess_PlaceNewOrderService::REGISTRY_ITEM_KEY);
        $itemBefore = $itemBefore ? $itemBefore : Mage::registry($key);
        
        try{
            
            Magestore_Coresuccess_Model_Service::placeNewOrderService()->execute($item, $itemBefore);
            
        }catch(Exception $e) {
            /* log issue */
            Mage::log($e->getMessage(), null, 'inventorysuccess.log');
        }
    }
}