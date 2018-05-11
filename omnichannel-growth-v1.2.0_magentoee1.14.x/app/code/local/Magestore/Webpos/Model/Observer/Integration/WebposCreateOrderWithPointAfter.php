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

class Magestore_Webpos_Model_Observer_Integration_WebposCreateOrderWithPointAfter extends Magestore_Webpos_Model_Observer_Abstract
{
    /**
     * @param $observer
     * @return $this
     */
    public function execute($observer)
    {
        try{
            if (!$this->_helper->isRewardPointsEnable()) {
                return $this;
            }
            $order = $observer->getEvent()->getOrder();
            if(isset($order) && $order->getId() && $order->getCustomerId() && $order->getRewardpointsSpent() > 0){
                $customer = $this->_getModel('customer/customer')->load($order->getCustomerId());
                $action = $this->_getHelper('rewardpoints/action');
                $action->addTransaction('spending_order',
                    $customer,
                    $order
                );
            }
        }catch(Exception $e){
            Mage::log($e->getMessage(), null, 'system.log', true);
        }
    }
}