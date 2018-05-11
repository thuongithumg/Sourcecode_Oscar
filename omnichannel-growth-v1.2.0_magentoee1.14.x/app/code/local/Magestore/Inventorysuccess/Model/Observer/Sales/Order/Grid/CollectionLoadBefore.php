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
 * Class Magestore_Inventorysuccess_Model_Observer_Sales_Order_Grid_CollectionLoadBefore
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Observer_Sales_Order_Grid_CollectionLoadBefore
{
    /**
     * Filter grid order by permission
     *
     * @param $observer
     * @return $this
     */
    public function execute($observer)
    {
        /** @var Mage_Sales_Model_Resource_Order_Grid_Collection $collection */
        $collection = $observer->getEvent()->getOrderGridCollection();
        $orderProcessService = Magestore_Coresuccess_Model_Service::orderProcessService();
        if ($orderProcessService->canChangeOrderWarehouse()) {
            return $this;
        }
        $warehouseCollection = $orderProcessService->getViewWarehouseList();
        $collection->addFieldToFilter('warehouse_id', array('in' => $warehouseCollection->getAllIds()));
        return $this;
    }
}