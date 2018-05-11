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
class Magestore_Inventorysuccess_Model_Service_OrderProcess_CreditmemoFormService
{
    /**
     * Get list of available warehouses to return items
     * 
     * @return array
     */
    public function getAvailableWarehouses()
    {   
        $availableWarehouses = array();
        /* filter permission */
        $warehouses = Magestore_Coresuccess_Model_Service::orderProcessService()->getCreditmemoWarehouseList();
        if($warehouses->getSize()) {
            foreach($warehouses as $warehouse) {
                $availableWarehouses[$warehouse->getId()] = $warehouse->getWarehouseName() . ' ('.$warehouse->getWarehouseCode().')';
            }
        }
        return $availableWarehouses;
    }
}