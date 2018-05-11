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
 * RewardPointsLoyaltyLevel Observer Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsLoyaltyLevel
 * @author      Magestore Developer
 */
class Magestore_RewardPointsLoyaltyLevel_Model_Observer {

    /**
     * add field to customer rewardpoints form
     */
    public function prepareCustomerRewardForm($observer) {
        $form = $observer['form'];
        $fieldset = $form->getElement('rewardpoints_form');
        if (Mage::helper('rewardpoints')->isEnableLoyalty()) {
            $loyalty_collection = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->getCollection()->addFieldToFilter('status', Magestore_RewardPointsLoyaltyLevel_Model_System_Config_Source_Status::STATUS_ENABLED);
            $level_option = array();
            foreach ($loyalty_collection as $level) {
                $loyalty = array('value' => $level->getId(),
                    'label' => $level->getLevelName());
                $level_option[] = $loyalty;
            }

            $customerId = Mage::app()->getRequest()->getParam('id');
            $customer = Mage::getModel('rewardpoints/customer')
                    ->load($customerId, 'customer_id');
            if (count($level_option)) {
                $fieldset->addField('level_id', 'select', array(
                    'label' => Mage::helper('rewardpoints')->__('Loyalty Level'),
                    'name' => 'rewardpoints[level_id]',
                    'values' => $level_option,
                    'value' => $customer->getLevelId()
                ));
            }
        }
    }

    /**
     * save loyalty level when save customer
     */
    public function customerSaveAfter($observer) {
        $customer = $observer['customer'];
        $params = Mage::app()->getRequest()->getParam('rewardpoints');
        // Update reward account settings
        $rewardAccount = Mage::getModel('rewardpoints/customer')->load($customer->getId(), 'customer_id');
        $rewardAccount->setCustomerId($customer->getId());
        $rewardAccount->setLevelId($params['level_id']);
        try {
            $rewardAccount->save();
        } catch (Exception $ex) {
            
        }
        return $this;
    }

    public function customerGroupSaveAfter($observer) {
        $group = $observer->getDataObject();
        $loyalty = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->load($group->getId(), 'customer_group_id');
        $loyalty->setLevelName($group->getCustomerGroupCode());
        $loyalty->setCustomerGroupId($group->getId());
        try {
            $loyalty->save();
        } catch (Exception $exc) {
            Mage::log($exc->getMessage());
        }
        return $this;
    }

    /**
     * add field to earning rate form
     * @param type $observer
     */
    public function prepareEarnRateForm($observer) {
        $form = $observer['form'];
        $data = $observer['data'];
        if (!is_null(Mage::app()->getRequest()->getParam('group_id'))) {
            $data['customer_group_ids'] = Mage::app()->getRequest()->getParam('group_id');
        }
//        $fieldset = $form->getElement('rewardpoints_form');
//        if (Mage::helper('rewardpoints')->isEnableLoyalty()) {
//            $loyalty_collection = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->getCollection()->addFieldToFilter('status', Magestore_RewardPointsLoyaltyLevel_Model_System_Config_Source_Status::STATUS_ENABLED);
//            $level_option = array();
//            foreach ($loyalty_collection as $level) {
//                $loyalty = array('value' => $level->getId(),
//                    'label' => $level->getLevelName());
//                $level_option[] = $loyalty;
//            }
//            if (count($level_option)) {
//                $fieldset->addField('use_level', 'select', array(
//                    'label' => Mage::helper('rewardpoints')->__('Use Loyalty Level'),
//                    'title' => Mage::helper('rewardpoints')->__('Use Loyalty Level'),
//                    'name' => 'use_level',
//                    'onchange' => 'hiddenLoyaltyLevel()',
//                    'values' => array(
//                        array(
//                            'value' => 0,
//                            'label' => Mage::helper('rewardpoints')->__('No'),
//                        ),
//                        array(
//                            'value' => 1,
//                            'label' => Mage::helper('rewardpoints')->__('Yes'),
//                        ),
//                    ),
//                        ), 'customer_group_ids');
//                $fieldset->addField('level_id', 'select', array(
//                    'label' => Mage::helper('rewardpoints')->__('Loyalty Level'),
//                    'name' => 'level_id',
//                    'values' => $level_option,
//                        ), 'use_level');
//            }
        $form->setValues($data);
        return $this;
    }

    /**
     * add field to spending rate form
     * @param type $observer
     */
    public function prepareSpendRateForm($observer) {
        $form = $observer['form'];
        $data = $observer['data'];
        if (!is_null(Mage::app()->getRequest()->getParam('group_id'))) {
            $data['customer_group_ids'] = Mage::app()->getRequest()->getParam('group_id');
        }
//        $fieldset = $form->getElement('rewardpoints_form');
//        if (Mage::helper('rewardpoints')->isEnableLoyalty()) {
//            $loyalty_collection = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->getCollection()->addFieldToFilter('status', Magestore_RewardPointsLoyaltyLevel_Model_System_Config_Source_Status::STATUS_ENABLED);
//            $level_option = array();
//            foreach ($loyalty_collection as $level) {
//                $loyalty = array('value' => $level->getId(),
//                    'label' => $level->getLevelName());
//                $level_option[] = $loyalty;
//            }
//            if (count($level_option)) {
//                $fieldset->addField('use_level', 'select', array(
//                    'label' => Mage::helper('rewardpoints')->__('Use Loyalty Level'),
//                    'title' => Mage::helper('rewardpoints')->__('Use Loyalty Level'),
//                    'name' => 'use_level',
//                    'onchange' => 'hiddenLoyaltyLevel()',
//                    'values' => array(
//                        array(
//                            'value' => 0,
//                            'label' => Mage::helper('rewardpoints')->__('No'),
//                        ),
//                        array(
//                            'value' => 1,
//                            'label' => Mage::helper('rewardpoints')->__('Yes'),
//                        ),
//                    ),
//                        ), 'customer_group_ids');
//                $fieldset->addField('level_id', 'select', array(
//                    'label' => Mage::helper('rewardpoints')->__('Loyalty Level'),
//                    'name' => 'level_id',
//                    'values' => $level_option,
//                        ), 'use_level');
//            }
        $form->setValues($data);
//        }
        return $this;
    }

    public function settingsPost($observer) {
        if (!Mage::helper('rewardpointsloyaltylevel')->isEnable())
            return $this;

        $action = $observer->getEvent()->getControllerAction();
        $notification = $action->getRequest()->getParam('loyalty_notification');
        if (Mage::getSingleton('customer/session')->isLoggedIn()
        ) {
            $rewardAccount = Mage::helper('rewardpointsloyaltylevel')->getCustomer();
            if (!$rewardAccount->getId()) {
                $rewardAccount->setCustomerId($customerId)
                        ->setData('point_balance', 0)
                        ->setData('holding_balance', 0)
                        ->setData('spent_balance', 0);
            }
            $rewardAccount->setLoyaltyNotification((boolean) $notification);
            try {
                $rewardAccount->save();
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
            }
        }
        return $this;
    }

    public function expiredLoyaltyLevel() {
        $customers = Mage::getModel('rewardpoints/customer')->getCollection()
                ->addFieldToFilter('loyalty_expire', array('to' => date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time()))))
                ->addFieldToFilter('loyalty_expire', array('notnull' => true));
        foreach ($customers as $customer) {
            $customer->setData('loyalty_expire', null)
                    ->save();
            $customerSystem = Mage::getModel('customer/customer')->load($customer->getCustomerId());
            if ($customerSystem->getId()) {
                $customerSystem->setGroupId('1');
                $customerSystem->save();
            }
        }
        return $this;
    }

    public function expiredGroupWhenLogin($observer) {
        $customer = $observer->getEvent()->getCustomer();
        if ($customer->getGroupId() == 1)
            return $this;
        $rewardCustomer = Mage::getModel('rewardpoints/customer')->load($customer->getId(), 'customer_id');
        if ($rewardCustomer->getId()) {
            $time_expired = $rewardCustomer->getLoyaltyExpire();
            if ($time_expired == null)
                return $this;
            if (strtotime($time_expired) <= Mage::getModel('core/date')->timestamp(time())) {
                try {
                    $rewardCustomer->setData('loyalty_expire', null)
                            ->save();
                    $customer->setGroupId('1')->save();
                } catch (Exception $e) {
                    
                }
            }
        }
        return $this;
    }

    public function emailExpiredLoyaltyLevel() {
        if (!Mage::getStoreConfig('rewardpoints/loyaltylevelplugin/loyalty_before_expired'))
            return $this;

        $date_befores = Mage::getStoreConfig('rewardpoints/loyaltylevelplugin/loyalty_before_expired_day');
        $now = date('Y-m-d', Mage::getModel('core/date')->timestamp(time()));
        $tomorow = $date_befores + 1;
        $customersReward = Mage::getModel('rewardpoints/customer')->getCollection()
                ->addFieldToFilter('loyalty_expire', array('from' => date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(strtotime("$now + $date_befores days")))))
                ->addFieldToFilter('loyalty_expire', array('to' => date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(strtotime("$now + $tomorow days")))));
        foreach ($customersReward as $customerReward) {
            if (!$customerReward->getLoyaltyNotification())
                continue;

            $customer = Mage::getModel('customer/customer')->load($customerReward->getCustomerId());
            $store = Mage::app()->getStore();
            $translate = Mage::getSingleton('core/translate');
            $translate->setTranslateInline(false);

            Mage::getModel('core/email_template')
                    ->setDesignConfig(array(
                        'area' => 'frontend',
                        'store' => $store->getId()
                    ))->sendTransactional(
                    Mage::getStoreConfig('rewardpoints/loyaltylevelplugin/loyalty_before_expired_template', $store), Mage::getStoreConfig(Magestore_RewardPoints_Model_Transaction::XML_PATH_EMAIL_SENDER, $store), $customer->getEmail(), $customer->getName(), array(
                'store' => $store,
                'customer' => $customer,
                'levelName' => Mage::getModel('customer/group')->load($customer->getGroupId())->getCustomerGroupCode(),
                'expirationdays' => $date_befores,
                    )
            );

            $translate->setTranslateInline(true);
        }
        return $this;
    }

    public function prepareFormCatalogAdminhtml($observer) {
//        if (Mage::getStoreConfig('rewardpoints/loyaltylevelplugin/enable_promotion')) {
        $form = $observer['form'];
        if (!is_null(Mage::app()->getRequest()->getParam('group_id'))) {
            $data['customer_group_ids'] = Mage::app()->getRequest()->getParam('group_id');
            $form->addValues($data);
        }
//            $fieldset = $form->getElement('base_fieldset');
//            $fieldset->addField('loyalty_level_use', 'select', array(
//                'name' => 'loyalty_level_use',
//                'label' => Mage::helper('rewardpointsloyaltylevel')->__('Use Loyalty Level'),
//                'title' => Mage::helper('rewardpointsloyaltylevel')->__('Use Loyalty Level'),
//                'required' => true,
//                'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
//                'onchange' => "if($('rule_loyalty_level_use').value == 0) $('rule_loyalty_level_id').up('tr').hide();"
//                . "else $('rule_loyalty_level_id').up('tr').show();"
//                    ), 'customer_group_ids');
//            $fieldset->addField('loyalty_level_id', 'multiselect', array(
//                'name' => 'loyalty_level_id[]',
//                'label' => Mage::helper('rewardpointsloyaltylevel')->__('Loyalty Level'),
//                'title' => Mage::helper('rewardpointsloyaltylevel')->__('Loyalty Level'),
//                'required' => true,
//                'values' => Mage::getResourceModel('rewardpointsloyaltylevel/loyaltylevel_collection')->toOptionArray(),
//                'note' => "<script type='text/javascript'>if($('rule_loyalty_level_use').value == 0) $('rule_loyalty_level_id').up('tr').hide();</script>"
//                    ), 'loyalty_level_use');
//
//            $model = Mage::registry('current_promo_catalog_rule');
//            $arrayValue = array(
//                'loyalty_level_use' => $model->getData('loyalty_level_use'),
//                'loyalty_level_id' => $model->getData('loyalty_level_id')
//            );
//            if (Mage::app()->getRequest()->getParam('level_id')) {
//                $arrayValue = array(
//                    'loyalty_level_use' => 1,
//                    'loyalty_level_id' => Mage::app()->getRequest()->getParam('level_id')
//                );
//            }
        // $form->addValues($data);
//        }
        return $this;
    }

    public function prepareFormShoppingcartAdminhtml($observer) {
//        if (Mage::getStoreConfig('rewardpoints/loyaltylevelplugin/enable_promotion')) {
        $form = $observer['form'];
        if (!is_null(Mage::app()->getRequest()->getParam('group_id'))) {
            $data['customer_group_ids'] = Mage::app()->getRequest()->getParam('group_id');
            $form->addValues($data);
        }
//        $fieldset = $form->getElement('base_fieldset');
//        $fieldset->addField('loyalty_level_use', 'select', array(
//            'name' => 'loyalty_level_use',
//            'label' => Mage::helper('rewardpointsloyaltylevel')->__('Use Loyalty Level'),
//            'title' => Mage::helper('rewardpointsloyaltylevel')->__('Use Loyalty Level'),
//            'required' => true,
//            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
//            'onchange' => "if($('rule_loyalty_level_use').value == 0) $('rule_loyalty_level_id').up('tr').hide();"
//            . "else $('rule_loyalty_level_id').up('tr').show();"
//                ), 'customer_group_ids');
//        $fieldset->addField('loyalty_level_id', 'multiselect', array(
//            'name' => 'loyalty_level_id[]',
//            'label' => Mage::helper('rewardpointsloyaltylevel')->__('Loyalty Level'),
//            'title' => Mage::helper('rewardpointsloyaltylevel')->__('Loyalty Level'),
//            'required' => true,
//            'values' => Mage::getResourceModel('rewardpointsloyaltylevel/loyaltylevel_collection')->toOptionArray(),
//            'note' => "<script type='text/javascript'>if($('rule_loyalty_level_use').value == 0) $('rule_loyalty_level_id').up('tr').hide();</script>"
//                ), 'loyalty_level_use');
//
//        $model = Mage::registry('current_promo_quote_rule');
//        $arrayValue = array(
//            'loyalty_level_use' => $model->getData('loyalty_level_use'),
//            'loyalty_level_id' => $model->getData('loyalty_level_id')
//        );
//        if (Mage::app()->getRequest()->getParam('level_id')) {
//            $arrayValue = array(
//                'loyalty_level_use' => 1,
//                'loyalty_level_id' => Mage::app()->getRequest()->getParam('level_id')
//            );
//        }
        // $form->addValues($data);
//        }
        return $this;
    }

    public function salesruleValidatorProcess($observer) {
        $needConvert = Mage::getStoreConfig('rewardpoints/loyaltylevelplugin/enable_promotion');
        if (!$needConvert)
            return $this;
        $rule = $observer['rule'];
        if (!$rule->getLoyaltyLevelUse())
            return $this;

        $customer_reward = Mage::helper('rewardpoints/customer')->getAccount();
        if (!$customer_reward->getId() || $customer_reward->getLevelId() != $rule->getLoyaltyLevelId()) {
            $result = $observer['result'];
            $result['discount_amount'] = 0;
            $result['base_discount_amount'] = 0;
            if ($rule->getCouponType() != Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON) {
                $address = $observer['address'];
                $quote = $observer['quote'];
                $address->setCouponCode('');
                $quote->setCouponCode('');
            }
        }
        return $this;
    }

    public function setAvailableFilter($observer) {
        $collection = $observer['collection'];
        $level_id = $observer['level_id'];
        if ($level_id == null) {
            if (Mage::app()->getStore()->isAdmin()) {
                $customer = Mage::getSingleton('adminhtml/session_quote')->getCustomer();
                $customerReward = Mage::getModel('rewardpoints/customer')->load($customer->getId(), 'customer_id');
                if ($customerReward->getId())
                    $level_id = $customerReward->getLevelId();
                else
                    $level_id = 0;
            } else {
                $level_id = Mage::helper('rewardpointsloyaltylevel')->getCustomerLevel();
            }
        }
        if ($level_id) {
            $collection->getSelect()->where("(use_level = 0) OR ((use_level = 1) AND (level_id = ?))", $level_id);
        } else
            $collection->getSelect()->where("use_level = 0");
        return $this;
    }

    public function setFilterCategory($observer) {
        $collections = $observer->getEvent()->getCollection(); //$observer['collection'];
        $level_id = Mage::helper('rewardpointsloyaltylevel')->getCustomerLevel();
        if ($level_id) {
            $check = false;
            foreach ($collections as $collection) {
                if ($collection->getLevelId() == $level_id) {
                    $check = true;
                    break;
                }
            }
            if ($check) {
                $collections->addFieldToFilter('level_id', $level_id);
                return $this;
            }
        }
        $collections->addFieldToFilter('level_id', '0');
        return $this;
    }

    /**
     * auto join point level
     * @param type $observer
     * @return \Magestore_RewardPointsLoyaltyLevel_Model_Observer
     */
    public function transactionSaveAfter($observer) {
        $transaction = $observer['rewardpoints_transaction'];
        if ($transaction->getStatus() != Magestore_RewardPoints_Model_Transaction::STATUS_COMPLETED) {
            return $this;
        }
        if ($transaction->getActionType() == Magestore_RewardPoints_Model_Transaction::ACTION_TYPE_SPEND) {
            return $this;
        }
        
        $rewardAccount = $transaction->getRewardAccount();
        // get all level can auto join and condition type is Point
        $pointLevels = Mage::getResourceModel('rewardpointsloyaltylevel/loyaltylevel_collection')
                ->addFieldToFilter('status', Magestore_RewardPointsLoyaltyLevel_Model_Loyaltylevel::STATUS_ENABLED)
                ->addFieldToFilter('auto_join', 1)
                ->addFieldToFilter('condition_type', Magestore_RewardPointsLoyaltyLevel_Model_Loyaltylevel::CONDITION_TYPE_POINT)
                ->addFieldToFilter('condition_value', array('lteq' => $rewardAccount->getAccumulatedPoints()))
                ->setOrder('condition_value', 'DESC');
        if (!$pointLevels->getSize()) {
            return $this;
        }
        $highestLevel = $pointLevels->getFirstItem();
        $this->compareAndAssign($rewardAccount, $highestLevel);
        return $this;
    }

    public function salesOrderInvoiceSaveAfter($observer) {
        $invoice = $observer['invoice'];
        $order = $invoice->getOrder();
        $customerId = $order->getCustomerId();
        if ($order->getCustomerIsGuest() || !$customerId || $invoice->getState() != Mage_Sales_Model_Order_Invoice::STATE_PAID) {
            return $this;
        }
        $rewardAccount = Mage::getModel('rewardpoints/customer')->load($customerId, 'customer_id');
        if (!$rewardAccount->getId()) {
            return $this;
        }
        // get all available sales level
        $salesLevels = Mage::getResourceModel('rewardpointsloyaltylevel/loyaltylevel_collection')
                ->addFieldToFilter('status', Magestore_RewardPointsLoyaltyLevel_Model_Loyaltylevel::STATUS_ENABLED)
                ->addFieldToFilter('auto_join', 1)
                ->addFieldToFilter('condition_type', Magestore_RewardPointsLoyaltyLevel_Model_Loyaltylevel::CONDITION_TYPE_SALES)
                ->addFieldToFilter('condition_value', array('lteq' => $rewardAccount->getTotalSales()))
                ->setOrder('condition_value', 'DESC');
        if (!$salesLevels->getSize()) {
            return $this;
        }
        $highestLevel = $salesLevels->getFirstItem();

        $this->compareAndAssign($rewardAccount, $highestLevel);
        return $this;
    }

    public function compareAndAssign($rewardAccount, $highestLevel) {
        $currentLevel = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->loadByRewardCustomer($rewardAccount);
        if (!$currentLevel->getId()) {
            $highestLevel->setLevelForRewardCustomer($rewardAccount);
            return $this;
        }
        // current level is the highest.
        if ($currentLevel->getId() == $highestLevel->getId()) {
            return $this;
        }
        // for same condition type.
        if ($highestLevel->getConditionType() == $currentLevel->getConditionType() && $highestLevel->getConditionValue() > $currentLevel->getConditionValue()) {
            $highestLevel->setLevelForRewardCustomer($rewardAccount);
        }
        // for different condition type.
        if ($currentLevel->getPriority() >= $highestLevel->getPriority()) {
            return $this;
        }
        $highestLevel->setLevelForRewardCustomer($rewardAccount);
        return $this;
    }

}
