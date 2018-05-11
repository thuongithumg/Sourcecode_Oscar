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
 * RewardPointsLoyaltyLevel Helper
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsLoyaltyLevel
 * @author      Magestore Developer
 */
class Magestore_RewardPointsLoyaltyLevel_Helper_Data extends Mage_Core_Helper_Abstract {

    function getCustomerLevel() {
        $customer_group_id = Mage::getSingleton('customer/session')->getCustomerGroupId();
        if (in_array($customer_group_id, array('0', '1')))
            return null;
        $loyaltyLevel = Mage::getModel('rewardpointsloyaltylevel/loyaltylevel')->loadByGroup($customer_group_id);
        return $loyaltyLevel->getId();
    }

    function isEnable() {
        return Mage::getStoreConfig('rewardpoints/loyaltylevelplugin/enable', null) && Mage::helper('rewardpoints')->isEnable();
    }

    function getCustomer($customer_id = null){
        if($customer_id == null){
            $customer_id = Mage::getSingleton('customer/session')->getCustomerId();
        }
        if(!$customer_id) return null;
        $rewardAccount = Mage::getModel('rewardpoints/customer')->load($customer_id, 'customer_id');
        if(!$rewardAccount->getId()){
            $rewardAccount->setCustomerId($customer_id)
                ->setData('point_balance', 0)
                ->setData('holding_balance', 0)
                ->setData('spent_balance', 0)
                ->setData('is_notification', 0)
                ->setData('expire_notification', 0)
                ->save();
        }
        
        return $rewardAccount;
    }
    /**
     * 
     * @param type $level_id
     * @return \Varien_Object
     */
    public function getAllEarnRuleResponsive($customer_group_id, $website_id = null) {
        $earning_loyalty = array();
        /**
         * earning rate
         */
        $earning_rate = Mage::getModel('rewardpoints/rate')->getCollection()
                ->addFieldToFilter('direction', Magestore_RewardPoints_Model_Rate::MONEY_TO_POINT)
                ->addFieldToFilter('points', array('gt' => 0))
                ->addFieldToFilter('money', array('gt' => 0));
        if ($website_id)
            $earning_rate->addFieldToFilter('website_ids', array('finset' => $website_id));
        if (!is_null($customer_group_id))
            $earning_rate->addFieldToFilter('customer_group_ids', array('finset' => $customer_group_id));
        $earning_rate->getSelect()->order('sort_order DESC');
        $rate = $earning_rate->getFirstItem();
        if ($rate->getId()) {
            $rule = new Varien_Object();
            $rule->setId($rate->getId());
            $rule->setType('rate');
            $rule->setTitle($this->__('Earning rate'));
            $rule->setDescription($this->__('Each %s spent for order customer will earn %s', '<strong class="rewardpoints-money">' . Mage::helper('core')->formatPrice($rate->getMoney()) . '</strong>', '<strong class="rewardpoints-money">' . Mage::helper('rewardpoints/point')->format($rate->getPoints()) . '</strong>'));
            $rule->setDescriptionFrontend($this->__('Each %s spent for order you will earn %s', '<strong class="rewardpoints-money">' . Mage::helper('core')->formatPrice($rate->getMoney()) . '</strong>', '<strong class="rewardpoints-money">' . Mage::helper('rewardpoints/point')->format($rate->getPoints()) . '</strong>'));
            $earning_loyalty[] = $rule;
        }
        /**
         * earning rule
         */
        if (Mage::getConfig()->getModuleConfig('Magestore_RewardPointsRule')->is('active', 'true')) {
//            earning catalog
            $earning_catalog = Mage::getModel('rewardpointsrule/earning_catalog')->getCollection();
            $earning_catalog = $this->filterCollection($earning_catalog, $customer_group_id, $website_id);
            foreach ($earning_catalog as $catalog) {
                $rule = new Varien_Object();
                $rule->setId($catalog->getId());
                $rule->setType('catalog');
                $rule->setTitle($catalog->getName());
                $rule->setDescription($catalog->getDescription());
                if ($catalog->getDescription())
                    $rule->setDescriptionFrontend(': ' . $catalog->getDescription());
                $earning_loyalty[] = $rule;
            }
            //            earning sales
            $earning_sales = Mage::getModel('rewardpointsrule/earning_sales')->getCollection();
            $earning_sales = $this->filterCollection($earning_sales, $customer_group_id, $website_id);
            foreach ($earning_sales as $sale) {
                $rule = new Varien_Object();
                $rule->setId($sale->getId());
                $rule->setType('sales');
                $rule->setTitle($sale->getName());
                $rule->setDescription($sale->getDescription());
                if ($sale->getDescription())
                    $rule->setDescriptionFrontend(': ' . $sale->getDescription());
                $earning_loyalty[] = $rule;
            }
        }
        return $earning_loyalty;
    }

    public function getAllSpendingRuleResponsive($customer_group_id, $website_id = null) {
        $spending_loyalty = array();
        /**
         * earning rate
         */
        $spending_rate = Mage::getModel('rewardpoints/rate')->getCollection()
                ->addFieldToFilter('direction', Magestore_RewardPoints_Model_Rate::POINT_TO_MONEY)
                ->addFieldToFilter('points', array('gt' => 0))
                ->addFieldToFilter('money', array('gt' => 0));
        if ($website_id)
            $spending_rate->addFieldToFilter('website_ids', array('finset' => $website_id));
        if (!is_null($customer_group_id))
            $spending_rate->addFieldToFilter('customer_group_ids', array('finset' => $customer_group_id));
        $spending_rate->getSelect()->order('sort_order DESC');

        $rate = $spending_rate->getFirstItem();

        if($rate->getId()):
//        foreach ($spending_rate as $rate) {
            $rule = new Varien_Object();
            $rule->setId($rate->getId());
            $rule->setType('rate');
            $rule->setTitle($this->__('Spending rate'));
            $rule->setDescription($this->__('Each %s customer get %s discount', '<strong class="rewardpoints-money">' . Mage::helper('rewardpoints/point')->format($rate->getPoints()) . '</strong>', '<strong class="rewardpoints-money">' . Mage::helper('core')->formatPrice($rate->getMoney()) . '</strong>'));
            $rule->setDescriptionFrontend($this->__('Each %s spend you will get %s discount', '<strong class="rewardpoints-money">' . Mage::helper('rewardpoints/point')->format($rate->getPoints()) . '</strong>', '<strong class="rewardpoints-money">' . Mage::helper('core')->formatPrice($rate->getMoney()) . '</strong>'));
            $spending_loyalty[] = $rule;
//        }
        endif;
        /**
         * earning rule
         */
        if (Mage::getConfig()->getModuleConfig('Magestore_RewardPointsRule')->is('active', 'true')) {
//            earning catalog
            $spending_catalog = Mage::getModel('rewardpointsrule/spending_catalog')->getCollection();
            $spending_catalog = $this->filterCollection($spending_catalog, $customer_group_id, $website_id);
            foreach ($spending_catalog as $catalog) {
                $rule = new Varien_Object();
                $rule->setId($catalog->getId());
                $rule->setType('catalog');
                $rule->setTitle($catalog->getName());
                $rule->setDescription($catalog->getDescription());
                if ($catalog->getDescription())
                    $rule->setDescriptionFrontend(': ' . $catalog->getDescription());
                $spending_loyalty[] = $rule;
            }
            //            earning sales
            $spending_sales = Mage::getModel('rewardpointsrule/spending_sales')->getCollection();
            $spending_sales = $this->filterCollection($spending_sales, $customer_group_id, $website_id);
            foreach ($spending_sales as $sale) {
                $rule = new Varien_Object();
                $rule->setId($sale->getId());
                $rule->setType('sales');
                $rule->setTitle($sale->getName());
                $rule->setDescription($sale->getDescription());
                if ($sale->getDescription())
                    $rule->setDescriptionFrontend(': ' . $sale->getDescription());
                $spending_loyalty[] = $rule;
            }
        }
        return $spending_loyalty;
    }

    public function getAllPromoSpendingRuleResponsive($customer_group_id, $website_id = null) {
        $spending_loyalty = array();
        $promo_shoppingcart = Mage::getModel('salesrule/rule')->getCollection()
                ->addFieldToFilter('coupon_type', Mage_SalesRule_Model_Rule::COUPON_TYPE_NO_COUPON)
                ->addFieldToFilter('from_date', array(
                    array('to' => date('Y-m-d')),
                    array('null' => true),
                ))
                ->addFieldToFilter('to_date', array(
                    array('from' => date('Y-m-d')),
                    array('null' => true),
                ))
                ->addFieldToFilter('is_active', true);
        if (version_compare(Mage::getVersion(), '1.7.0.0', '<')) {
            $promo_shoppingcart->addFieldToFilter('customer_group_ids', array('finset' => $customer_group_id));
            if ($website_id != null)
                $promo_shoppingcart->addFieldToFilter('website_ids', array('finset' => $website_id));
        }else {
            if (!is_null($customer_group_id))
                $promo_shoppingcart->getSelect()->join(array('cgi' => Mage::getModel('core/resource')->getTableName('salesrule_customer_group')), "main_table.rule_id = cgi.rule_id AND cgi.customer_group_id = $customer_group_id", array("*"));
            if ($website_id != null)
                $promo_shoppingcart->getSelect()->join(array('wi' => Mage::getModel('core/resource')->getTableName('salesrule_website')), "main_table.rule_id = wi.rule_id AND wi.website_id = $website_id", array("*"));
        }

        foreach ($promo_shoppingcart as $promo_rule) {
            $rule = new Varien_Object();
            $rule->setId($promo_rule->getId());
            $rule->setType("shopping_discount");
            $rule->setTitle($promo_rule->getName());
            $rule->setDescription($promo_rule->getDescription());
            $spending_loyalty[] = $rule;
        }


        $promo_catalog = Mage::getModel('catalogrule/rule')->getCollection()
                ->addFieldToFilter('from_date', array(
                    array('to' => date('Y-m-d')),
                    array('null' => true),
                ))
                ->addFieldToFilter('to_date', array(
                    array('from' => date('Y-m-d')),
                    array('null' => true),
                ))
                ->addFieldToFilter('is_active', true);
        if (version_compare(Mage::getVersion(), '1.7.0.0', '<')) {
            $promo_catalog->addFieldToFilter('customer_group_ids', array('finset' => $customer_group_id));
            if ($website_id != null)
                $promo_catalog->addFieldToFilter('website_ids', array('finset' => $website_id));
        }else {
            if (!is_null($customer_group_id))
                $promo_catalog->getSelect()->join(array('cgi' => Mage::getModel('core/resource')->getTableName('catalogrule_customer_group')), "main_table.rule_id = cgi.rule_id AND cgi.customer_group_id = $customer_group_id", array("*"));
            if ($website_id != null)
                $promo_catalog->getSelect()->join(array('wi' => Mage::getModel('core/resource')->getTableName('catalogrule_website')), "main_table.rule_id = wi.rule_id AND wi.website_id = $website_id", array("*"));
        }

        foreach ($promo_catalog as $promo_rule) {
            $rule = new Varien_Object();
            $rule->setId($promo_rule->getId());
            $rule->setType("catalog_discount");
            $rule->setTitle($promo_rule->getName());
            $rule->setDescription($promo_rule->getDescription());
            $spending_loyalty[] = $rule;
        }
        return $spending_loyalty;
    }

    public function filterCollection($collection, $customer_group_id, $website_id) {
        $collection->addFieldToFilter('is_active', 1)
                ->addFieldToFilter('from_date', array(
                    array('to' => date('Y-m-d')),
                    array('null' => true),
                ))
                ->addFieldToFilter('to_date', array(
                    array('from' => date('Y-m-d')),
                    array('null' => true),
        ));
        if ($website_id)
            $collection->addFieldToFilter('website_ids', array('finset' => $website_id));
        if ($customer_group_id)
            $collection->addFieldToFilter('customer_group_ids', array('finset' => $customer_group_id));
        $collection->getSelect()->order('sort_order DESC');
        return $collection;
    }

}
