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
class Magestore_Inventorysuccess_Model_Observer_Sales_OrderItemSaveBefore
{
    /**
     * 
     * @param type $observer
     */
    public function execute($observer)
    {
        /** @var Mage_Sales_Model_Order_Item $orderItem */
        $orderItem = $observer->getEvent()->getItem();
        $this->convertConfigurableItemQty($orderItem);
        $beforeItem = Mage::getModel('sales/order_item');
        if($orderItem->getId()) {
            $beforeItem->load($orderItem->getId());
        }
        $key = Magestore_Inventorysuccess_Model_Service_OrderProcess_PlaceNewOrderService::REGISTRY_ITEM_KEY . $orderItem->getId();
        if (!Mage::registry($key)) {
            Mage::register($key, $beforeItem);
        }
    }

    /**
     * Convert order item qty from configurable product to children items
     *
     * @param Mage_Sales_Model_Order_Item $orderItem
     */
    public function convertConfigurableItemQty($orderItem)
    {
        $parentItem = $orderItem->getParentItem();
        if ($parentItem && $parentItem->getProductType() == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            $orderItem->setQtyCanceled($parentItem->getQtyCanceled());
            $orderItem->setQtyInvoiced($parentItem->getQtyInvoiced());
            $orderItem->setQtyShipped($parentItem->getQtyShipped());
            $orderItem->setQtyRefunded($parentItem->getQtyRefunded());
            $orderItem->setQtyReturned($parentItem->getQtyReturned());
        }
    }
}