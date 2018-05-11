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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

class Magestore_Webpos_Model_Observer_Inventorysuccess_CreateCreditmemoAdjuststockData
        extends Magestore_Webpos_Model_Observer_Abstract
{
    /**
     * Load linked Warehouse from Location of WebPOS Order
     * 
     * @param $observer
     * @return $this
     */
    public function execute($observer)
    {
        $adjustDataObject = $observer->getEvent()->getAdjuststockData();
        $order = $observer->getEvent()->getOrder();
        if(Mage::getSingleton('admin/session')->isLoggedIn()) {
            return $this; 
        }
        if(!$userId = $order->getData('webpos_staff_id'))
            return $this;
        
        /* load staff from WebPOS */
        $staff = Mage::getModel('webpos/user')->load($userId);
        if($staff->getId()) {
            $createdBy = $staff->getUsername();
            $confirmedBy = $staff->getUsername();
        } else {
            $createdBy = 'webpos_staff';
            $confirmedBy = 'webpos_staff';
        }
        
        $adjustDataObject->setData('created_by', $createdBy);
        $adjustDataObject->setData('confirmed_by', $confirmedBy);
        
        return $this;
    }    

}