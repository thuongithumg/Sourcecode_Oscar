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

class Magestore_Inventorysuccess_Model_Observer_Integration_Webpos_LocationSaveAfter
{
    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute($observer)
    {
        $location = $observer->getDataObject();
        $warehouseId = $location->getWarehouseId();
        if($warehouseId == null || !$location->getLocationId()){
            return $this;
        }
        $locationService = Magestore_Coresuccess_Model_Service::locationService();
        $locationService->mappingWarehouseToLocation($warehouseId,$location->getLocationId());
        return $this;
    }
}