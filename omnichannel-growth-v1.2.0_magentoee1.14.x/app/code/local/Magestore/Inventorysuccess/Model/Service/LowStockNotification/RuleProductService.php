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
 * Inventorysuccess Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Service_LowStockNotification_RuleProductService
{
    /**
     * Store matched product Ids
     *
     * @var array
     */
    protected $_productIds;

    /**
     * Get array of product ids which are matched by rule
     * @param Magestore_Inventorysuccess_Model_LowStockNotification_Rule $ruleModel
     * @return array
     */
    public function getListProductIdsInRule(Magestore_Inventorysuccess_Model_LowStockNotification_Rule $ruleModel) {
        if (is_null($this->_productIds)) {
            $this->_productIds = array();
            $ruleModel->setCollectedAttributes(array());

            /** @var $productCollection Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection */
            $productCollection = Mage::getResourceModel('catalog/product_collection');
            $ruleModel->getConditions()->collectValidatedAttributes($productCollection);

            Mage::getSingleton('core/resource_iterator')->walk(
                $productCollection->getSelect(),
                array(array($this, 'callbackValidateProduct')),
                array(
                    'attributes' => $ruleModel->getCollectedAttributes(),
                    'product'    => Mage::getModel('catalog/product'),
                    'rule_model'    => $ruleModel
                )
            );
        }

        return $this->_productIds;
    }

    /**
     * Callback function for product matching
     *
     * @param $args
     * @return void
     */
    public function callbackValidateProduct($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);
        /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Rule $ruleModel */
        $ruleModel = $args['rule_model'];

        $results = array();
        foreach ($ruleModel->_getWebsitesMap() as $websiteId => $defaultStoreId) {
            $product->setStoreId($defaultStoreId);
            if ($ruleModel->getConditions()->validate($product)
                && !in_array($product->getId(), $this->_productIds)) {
                $this->_productIds[] = $product->getId();
            }
        }
    }

    /**
     * Apply rule
     * @param Magestore_Inventorysuccess_Model_LowStockNotification_Rule $ruleModel
     */
    public function applyRule(Magestore_Inventorysuccess_Model_LowStockNotification_Rule $ruleModel)
    {
        if ($ruleModel->getId()) {
            $productIds = $this->getListProductIdsInRule($ruleModel);
            /** delete all products in rule */
            $this->deleteProductInRule($ruleModel);
            /** insert products for rule */
            $this->insertProductInRule($ruleModel, $productIds);
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('inventorysuccess')->__('Rule has been applied.'));
        }
    }

    /**
     * delete all products in rule
     * @param Magestore_Inventorysuccess_Model_LowStockNotification_Rule $ruleModel
     */
    public function deleteProductInRule(Magestore_Inventorysuccess_Model_LowStockNotification_Rule $ruleModel)
    {
        /** @var Magestore_Inventorysuccess_Model_Mysql4_LowStockNotification_Rule_Product_Collection $ruleProductCollection */
        $ruleProductCollection = Mage::getResourceModel('inventorysuccess/lowStockNotification_rule_product_collection');
        $ruleProductCollection->deleteProductInRule($ruleModel);
    }

    /**
     * insert products in rule
     * @param Magestore_Inventorysuccess_Model_LowStockNotification_Rule $ruleModel
     * @param array $productIds
     */
    public function insertProductInRule(Magestore_Inventorysuccess_Model_LowStockNotification_Rule $ruleModel, array $productIds)
    {
        /** @var Magestore_Inventorysuccess_Model_Mysql4_LowStockNotification_Rule_Product_Collection $ruleProductCollection */
        $ruleProductCollection = Mage::getResourceModel('inventorysuccess/lowStockNotification_rule_product_collection');
        $ruleProductCollection->insertProductInRule($ruleModel, $productIds);
    }
}