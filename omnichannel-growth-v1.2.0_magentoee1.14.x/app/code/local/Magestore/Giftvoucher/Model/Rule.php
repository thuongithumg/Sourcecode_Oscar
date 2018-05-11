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
 * @package     Magestore_Giftvoucher
 * @module     Giftvoucher
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Giftvoucher Rule Model
 * 
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @author      Magestore Developer
 */

class Magestore_Giftvoucher_Model_Rule extends Mage_CatalogRule_Model_Rule
{

    protected static $_priceRulesData = array();

    /**
     * Calculate price using catalog price rule of product
     *
     * @param Mage_Catalog_Model_Product $product
     * @param float $price
     * @return float|null
     */
    public function calcProductPriceRule(Mage_Catalog_Model_Product $product, $price)
    {
        $priceRules = null;
        $productId = $product->getId();
        $storeId = $product->getStoreId();
        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();
        if ($product->hasCustomerGroupId()) {
            $customerGroupId = $product->getCustomerGroupId();
        } else {
            $customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }
        $dateTs = Mage::app()->getLocale()->storeTimeStamp($storeId);
        $cacheKey = date('Y-m-d', $dateTs) . "|$websiteId|$customerGroupId|$productId|$price";

        if (!array_key_exists($cacheKey, self::$_priceRulesData)) {
            $rulesData = Mage::getResourceSingleton('giftvoucher/rule')
                ->getRulesFromProduct($dateTs, $websiteId, $customerGroupId, $productId);
            if ($rulesData) {
                foreach ($rulesData as $ruleData) {
                    $priceRules = $this->calcPriceRule(
                        $ruleData['simple_action'], $ruleData['discount_amount'], $priceRules ? $priceRules : $price);
                    if ($ruleData['stop_rules_processing']) {
                        break;
                    }
                }
                return self::$_priceRulesData[$cacheKey] = $priceRules;
            } else {
                self::$_priceRulesData[$cacheKey] = null;
            }
        } else {
            return self::$_priceRulesData[$cacheKey];
        }
        return null;
    }

    /**
     * Algorithm for calculating price rule
     *
     * @param  string $actionOperator
     * @param  int $ruleAmount
     * @param  float $price
     * @return float|int
     */
    public function calcPriceRule($actionOperator, $ruleAmount, $price)
    {
        $priceRule = 0;
        switch ($actionOperator) {
            case 'to_fixed':
                $priceRule = $ruleAmount;
                break;
            case 'to_percent':
                $priceRule = $price * $ruleAmount / 100;
                break;
            case 'by_fixed':
                $priceRule = $price - $ruleAmount;
                break;
            case 'by_percent':
                $priceRule = $price * (1 - $ruleAmount / 100);
                break;
        }
        return $priceRule;
    }

}
