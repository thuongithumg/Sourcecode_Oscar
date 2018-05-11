<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 *   @category    Magestore
 *   @package     Magestore_Reportsuccess
 *   @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Reportsuccess Observer
 *
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */
class Magestore_Reportsuccess_Model_Observer_Salesreport_salesOrderPlaceAfter
{
    /**
     * @param type $observer
     */
    protected $_itemIds = array();

    /**
     * @param $observer
     * @return bool
     */
    public function execute($observer)
    {
        $order = $observer->getOrder();
        $items = $order->getAllItems();
        //$model = Mage::getModel('reportsuccess/costofgood');
        $items_update = array();
        foreach ($items as $item) {
            if($item->getProduct()->isComposite()) {
                continue;
            }
            $items_update[$item->getId()] = $item->getProductId();
            $this->_itemIds[$item->getId()] = $item->getProductId();
        }
        if(empty($this->_itemIds)){
            return true;
        }
        return Mage::getSingleton('reportsuccess/service_inventoryreport_mac_macService')->updateMacInOrderItem($this->_itemIds);
    }
}