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
 * Inventorysuccess Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Service_Sales_OrderService
{
    /**
     * 
     * @param int $productId
     * @return array
     */
    public function getPendingShipOrderIds($productId = null)
    {
        return $this->getResource()->getPendingShipOrderIds($productId);
    }
    
    /**
     * 
     * @return Magestore_Inventorysuccess_Model_Mysql4_Sales_Order
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('inventorysuccess/sales_order');
    }
}