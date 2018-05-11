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
 * @package     Magestore_RewardPointsLoyaltyLevel
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardPointsLoyaltyLevel Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsLoyaltyLevel
 * @author      Magestore Developer
 */
class Magestore_RewardPointsLoyaltyLevel_IndexController extends Mage_Core_Controller_Front_Action {
    public function preDispatch() {
        parent::preDispatch();
        if (!$this->getRequest()->isDispatched()) {
            return;
        }
        if (!Mage::helper('rewardpointsloyaltylevel')->isEnable()) {
            $this->_redirect('customer/account');
            $this->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            return;
        }
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            Mage::getSingleton('customer/session')->setAfterAuthUrl(
                    Mage::getUrl($this->getFullActionName('/'))
            );
            $this->_redirect('customer/account/login');
            $this->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
        }
    }

    /**
     * index action
     */
    public function indexAction() {
        $rewardCustomer = Mage::getModel('rewardpoints/customer')->load(Mage::getSingleton('customer/session')->getCustomerId(), 'customer_id');
        if($rewardCustomer->getId()){
            $time_expired = $rewardCustomer->getLoyaltyExpire();
            if($time_expired != null){
                if(strtotime($time_expired) <= Mage::getModel('core/date')->timestamp(time())){
                    try{
                        $rewardCustomer->setData('loyalty_expire', null)
                                        ->save();
                        Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getCustomerId())->setGroupId('1')->save();
                    } catch(Exception $e) {

                    }
                }
            }
        }
        
        $customer_level = Mage::helper('rewardpointsloyaltylevel')->getCustomerLevel();
        $currentLevel = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->load($customer_level);
        if (!$currentLevel->getId() || $currentLevel->getStatus() == Magestore_RewardPointsLoyaltyLevel_Model_System_Config_Source_Status::STATUS_DISABLED) {
            $this->_redirect('rewardpointsloyaltylevel/join');
            return $this;
        }
        $this->loadLayout();
        $this->_title($this->__('Manage Loyalty Level'));
        $this->_initLayoutMessages('rewardpointsloyaltylevel/session');
        $this->renderLayout();
    }
}
