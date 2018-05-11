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
 * Adjuststock Resource Collection Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Mysql4_LowStockNotification_Rule_Product_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * @var int
     */
    protected $batchCount;

    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/lowStockNotification_rule_product');
        $this->batchCount = 1000;
    }

    /**
     * delete all products in rule
     * @param Magestore_Inventorysuccess_Model_LowStockNotification_Rule $ruleModel
     */
    public function deleteProductInRule(Magestore_Inventorysuccess_Model_LowStockNotification_Rule $ruleModel)
    {
        $query = $this->getConnection()->deleteFromSelect(
            $this->getConnection()
                ->select()
                ->from(Mage::getSingleton('core/resource')->getTableName('os_lowstock_notification_rule_product'), 'product_id')
                ->distinct()
                ->where('rule_id = ?', $ruleModel->getId()),
            Mage::getSingleton('core/resource')->getTableName('os_lowstock_notification_rule_product')
        );
        $this->getConnection()->query($query);
    }

    /**
     * insert products in rule
     * @param Magestore_Inventorysuccess_Model_LowStockNotification_Rule $ruleModel
     * @param $productIds
     */
    public function insertProductInRule(Magestore_Inventorysuccess_Model_LowStockNotification_Rule $ruleModel, $productIds)
    {
        $rows = array();
        $ruleId = $ruleModel->getId();
        foreach ($productIds as $productId) {
            $rows[] = array(
                'rule_id' => $ruleId,
                'product_id' => $productId
            );
            if (count($rows) == $this->batchCount) {
                $this->getConnection()->insertMultiple(Mage::getSingleton('core/resource')->getTableName('os_lowstock_notification_rule_product'), $rows);
                $rows = array();
            }
        }
        if (!empty($rows)) {
            $this->getConnection()->insertMultiple(Mage::getSingleton('core/resource')->getTableName('os_lowstock_notification_rule_product'), $rows);
        }
        $this->getConnection()->update(
            Mage::getSingleton('core/resource')->getTableName('os_lowstock_notification_rule'),
            array('apply' => Magestore_Inventorysuccess_Model_LowStockNotification_Rule::APPLIED),
            array('rule_id = ?' => $ruleId)
        );
    }
}