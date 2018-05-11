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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseordersuccess Model
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_LowStock
    extends Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_AbstractOption
{
    /**
     * @return array
     */
    public function getLowStockList() {
        /** @var Magestore_Inventorysuccess_Model_Mysql4_LowStockNotification_Notification_Collection $collection */
        $collection = Mage::getResourceModel('inventorysuccess/lowStockNotification_notification_collection');
        $collection->setOrder(Magestore_Inventorysuccess_Model_LowStockNotification_Notification::CREATED_AT, 'DESC');
        $collection->getSelect()->where(new \Zend_Db_Expr('created_at >= (NOW() - INTERVAL 1 MONTH )'));
        $options = array('' => Mage::helper('purchaseordersuccess')->__('Please select a low stock notification'));
        /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Notification $item */
        foreach ($collection as $item){
            $ruleId = $item->getRuleId();
            /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Rule $rule */
            $rule = Mage::getModel('inventorysuccess/lowStockNotification_rule')->load($ruleId);
            if(!$rule->getId())
                $rule = null;
            $label = '';
            if ($rule) {
                $label .= '['.$rule->getRuleName().']';
                $label .= '-['.date('Y-d-m', strtotime($item->getCreatedAt())).']';
            }
            if ($item->getWarehouseName()) {
                $label .= '-['.Mage::helper('purchaseordersuccess')->__('Warehouse: ').$item->getWarehouseName().']';
            } else {
                $label .= '-['.Mage::helper('purchaseordersuccess')->__('Global').']';
            }

            $options[$item->getId()] = $label;
        }
        return $options;
    }

    /**
     * Retrieve option array
     *
     * @return array()
     */
    public function getOptionHash()
    {
        return $this->getLowStockList();
    }
}