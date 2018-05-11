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
class Magestore_RewardPointsLoyaltyLevel_JoinController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        if (!$this->getRequest()->isDispatched()) {
            return ;
        }
        if (!Mage::helper('rewardpointsloyaltylevel')->isEnable()) {
            $this->_redirect('customer/account');
            $this->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
            return ;
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
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title($this->__('Manage Loyalty Level'));
        $this->_initLayoutMessages('rewardpointsloyaltylevel/session');
        if ($navigationBlock = $this->getLayout()->getBlock('rewardpoints.navigation')) {
            $navigationBlock->setActive('rewardpointsloyaltylevel/index/index');
        }
        $this->renderLayout();
    }
    public function joinAction()
    {
        $levelId = $this->getRequest()->getParam('id');
        $level = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->load($levelId);
        if(!$level->getId() || $level->getStatus() == Magestore_RewardPointsLoyaltyLevel_Model_System_Config_Source_Status::STATUS_DISABLED){
            Mage::getSingleton('rewardpointsloyaltylevel/session')->addError($this->__('Group does not exist.'));
            $this->_redirect('rewardpointsloyaltylevel');
            return $this;
        }
        $customer_group = $level->getCustomerGroupId();
        if($customer_group == Mage::getSingleton('customer/session')->getCustomerGroupId()){
            Mage::getSingleton('rewardpointsloyaltylevel/session')->addError($this->__('You cannot join this group again.'));
            $this->_redirect('rewardpointsloyaltylevel');
            return $this;
        }
        $group = Mage::getModel('customer/group')->load($customer_group);
        if(!$group->getId()){
            Mage::getSingleton('rewardpointsloyaltylevel/session')->addError($this->__('You cannot join this group.'));
            $this->_redirect('rewardpointsloyaltylevel');
            return $this;
        }
        
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if(!$customer->getId()) {
            $this->_redirect('customer/account/login');
            return $this;
        }
        
        $customerLevel = Mage::helper('rewardpointsloyaltylevel')->getCustomerLevel();
        if($customerLevel != null && $customerLevel == $level->getId()){
            Mage::getSingleton('rewardpointsloyaltylevel/session')->addNotice($this->__('You have joined this group.'));
            $this->_redirect('rewardpointsloyaltylevel');
            return $this;
        }
        $customerReward = Mage::helper('rewardpointsloyaltylevel')->getCustomer();
        $customerPoint = $customerReward->getPointBalance();
		$customerAccumulated = $customerReward->getAccumulatedPoints();
        if($customerAccumulated < $level->getConditionValue() || $customerPoint < $level->getDemeritPoints()){
            Mage::getSingleton('rewardpointsloyaltylevel/session')->addError($this->__('Your balance is not enough to join this group.'));
            $this->_redirect('rewardpointsloyaltylevel');
            return $this;
        }
        
        try{
            if($level->getDemeritPoints() > 0){
                Mage::helper('rewardpoints/action')->addTransaction('loyalty', $customer, $level, array());
            }
            $now = date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()));
            $period = $level->getRetentionPeriod();
            if($period && is_numeric($period)) $string_time = date('Y-m-d H:i:s', strtotime("$now + $period days"));
            else $string_time = null;
            Mage::getModel('rewardpoints/customer')->load($customer->getId(), 'customer_id')
                                                   ->setData('loyalty_expire', $string_time)
                                                   ->save();
            $customer = Mage::getModel('customer/customer')->load($customer->getId());
            $customer->setGroupId($level->getCustomerGroupId())
                     ->save();
        } catch (Exception $ex) {
            Mage::getSingleton('rewardpointsloyaltylevel/session')->addError($this->__('Cannot join group.'));
            $this->_redirect('rewardpointsloyaltylevel');
            return $this;
        }
        Mage::getSingleton('rewardpointsloyaltylevel/session')->addSuccess($this->__('You have joined this group successfully!'));
        $this->_redirect('rewardpointsloyaltylevel');
        return $this;
    }
    
}