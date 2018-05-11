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
class Magestore_Inventorysuccess_Model_Service_LowStockNotification_RuleService
{
    /**
     * get nex time to run notification
     * @param Magestore_Inventorysuccess_Model_LowStockNotification_Rule $ruleModel
     * @return string
     */
    public function getNewNextTime(Magestore_Inventorysuccess_Model_LowStockNotification_Rule $ruleModel)
    {
        /** @var Zend_Date $dateTime */
        $dateTime = new Zend_Date();
        $now = $dateTime->getTimestamp();

        /** if update time type is daily  */
        if ($ruleModel->getUpdateTimeType() == Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TIME_TYPE_DAILY) {
            $times = $ruleModel->getSpecificTime();
            $times = explode(',', $times);
            $nextDate = date('Y-m-d', $now);
            $timeNow = date('H', $now) + 1;
            $nextTime = '';
            foreach ($times as $time) {
                if ($timeNow <= $time) {
                    $nextTime = $time;
                    break;
                }
            }
            if (!$nextTime) {
                $nextTime = $times[0];
                $nextDate = date('Y-m-d', strtotime('+1 day', strtotime(date('Y-m-d', $now))));
            }
            $newNextTime = $nextDate. ' '. $nextTime. ':00:00';
            return $newNextTime;
        }
        /** if update time type is monthly */
        if ($ruleModel->getUpdateTimeType() == Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TIME_TYPE_MONTHLY) {
            $times = explode(',', $ruleModel->getSpecificTime());
            $days = explode(',', $ruleModel->getSpecificDay());
            $months = explode(',', $ruleModel->getSpecificMonth());
            $timeNow = date('H', $now);
            $dayNow = date('d', $now);
            $monthNow = date('m', $now);
            $yearNow = date('Y', $now);
            $nextYear = $yearNow;
            $nextMonth = '';
            $nextDay = '';
            $nextTime = '';
            /** compare month */
            foreach ($months as $month) {
                if ($monthNow <= $month) {
                    $nextMonth = $month;
                    break;
                }
            }
            if (!$nextMonth) {
                $nextYear = $yearNow + 1;
                $nextMonth = $months[0];
                $nextDay = $days[0];
                $nextTime = $times[0];
                $newNextTime = $nextYear . '-' . $nextMonth . '-'. $nextDay . ' ' . $nextTime . ':00:00';
                return $newNextTime;
            }
            if ($nextMonth > $monthNow) {
                $nextDay = $days[0];
                $nextTime = $times[0];
                $newNextTime = $nextYear . '-' . $nextMonth . '-'. $nextDay . ' ' . $nextTime . ':00:00';
                return $newNextTime;
            }
            /** compare day */
            foreach ($days as $day) {
                if ($dayNow <= $day) {
                    $nextDay = $day;
                    break;
                }
            }
            if (!$nextDay) {
                $nextYear = $yearNow;
                if ($nextMonth == $monthNow)
                    $nextMonth = $nextMonth + 1;
                if ($nextMonth > max($months)) {
                    $nextYear = $yearNow + 1;
                    $nextMonth = $months[0];
                } else {
                    foreach ($months as $month) {
                        if ($nextMonth <= $month) {
                            $nextMonth = $month;
                            break;
                        }
                    }
                }
                $nextDay = $days[0];
                $nextTime = $times[0];
                $newNextTime = $nextYear . '-' . $nextMonth . '-'. $nextDay . ' ' . $nextTime . ':00:00';
                return $newNextTime;
            }
            if ($nextDay > $dayNow) {
                $nextTime = $times[0];
                $newNextTime = $nextYear . '-' . $nextMonth . '-'. $nextDay . ' ' . $nextTime . ':00:00';
                return $newNextTime;
            }
            /** compare time */
            foreach ($times as $time) {
                if ($timeNow <= $time) {
                    $nextTime = $time;
                    break;
                }
            }
            if (!$nextTime) {
                $nextTime = $times[0];
                $nextDay = $nextDay + 1;
                if ($nextDay > max($days)) {
                    $nextDay = $days[0];
                    $nextMonth = $nextMonth + 1;
                    if ($nextMonth > max($months)) {
                        $nextYear = $yearNow + 1;
                        $nextMonth = $months[0];
                    } else {
                        foreach ($months as $month) {
                            if ($nextMonth <= $month) {
                                $nextMonth = $month;
                                break;
                            }
                        }
                        $nextYear = $yearNow;
                    }
                } else {
                    foreach ($days as $day) {
                        if ($nextDay <= $day) {
                            $nextDay = $day;
                            break;
                        }
                    }
                }
            }
            $newNextTime = $nextYear . '-' . $nextMonth . '-'. $nextDay . ' ' . $nextTime . ':00:00';
            return $newNextTime;
        }
    }

    /**
     * get available rules
     * @return array
     */
    public function getAvailableRules()
    {
        /** @var Magestore_Inventorysuccess_Model_Mysql4_LowStockNotification_Rule $ruleResourceModel */
        $ruleResourceModel = Mage::getResourceModel('inventorysuccess/lowStockNotification_rule');

        return $ruleResourceModel->getAvailableRules();
    }

    /**
     * @param array $rule
     */
    public function startNotification($rule) {
        /** @var Magestore_Inventorysuccess_Model_Service_LowStockNotification_NotificationService $notificationService */
        $notificationService = Magestore_Coresuccess_Model_Service::notificationService();
        /** @var Magestore_Inventorysuccess_Model_Mysql4_LowStockNotification_Rule_Product_Collection $ruleProductCollection */
        $ruleProductCollection = Mage::getResourceModel('inventorysuccess/lowStockNotification_rule_product_collection');
        $ruleProducts = $ruleProductCollection->addFieldToFilter('rule_id', $rule['rule_id']);
        if ($ruleProducts->getSize()) {
            $productSystem = $productWarehouse = '';
            $productIds = $ruleProducts->getColumnValues('product_id');
            $updateType = $rule['update_type'];
            if ($updateType == Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_BOTH_SYSTEM_AND_WAREHOUSE) {
                $warehouseIds = $rule['warehouse_ids'];
                $productSystem = $notificationService->getProductNotificationBySystem($rule, $productIds);
                $productWarehouse = $notificationService->getProductNotificationByWarehouse($rule, $productIds, $warehouseIds);
            }
            if ($updateType == Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_ONLY_WAREHOUSE) {
                $warehouseIds = $rule['warehouse_ids'];
                $productWarehouse = $notificationService->getProductNotificationByWarehouse($rule, $productIds, $warehouseIds);
            }
            if ($updateType == \Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_ONLY_SYSTEM) {
                $productSystem = $notificationService->getProductNotificationBySystem($rule, $productIds);
            }
            if ($productSystem || $productWarehouse) {
                $mailService = Magestore_Coresuccess_Model_Service::emailService();
                $mailService->sendEmailNotification($productSystem, $productWarehouse, $rule['notifier_emails']);
            }
        }
    }

    /**
     * Create default low-stock notification rule
     *
     * @return $this
     */
    public function createDefaultNotificationRule()
    {
        $normalLevel = array(
            'rule_name' => Mage::helper('inventorysuccess')->__('Normal level low stock notifications'),
            'lowstock_threshold' => 10,
            'sales_period' => 30,
            'from_date' => "",
            'to_date' => "",
            'status' => Magestore_Inventorysuccess_Model_LowStockNotification_Rule::STATUS_INACTIVE,
            'update_time_type' => Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TIME_TYPE_DAILY,
            'update_type' => Magestore_Inventorysuccess_Model_LowStockNotification_Rule::TYPE_ONLY_SYSTEM,
            'specific_time' => '00',
            'description' => Mage::helper('inventorysuccess')->__('Normal level low stock notifications'),
            'notifier_emails' => '',
            'warning_message' => '',
            'conditions' => array(
                1 => array(
                    'type' => 'Mage_CatalogRule_Model_Rule_Condition_Combine',
                    'aggregator' => 'all',
                    'value' => '1',
                    'new_child' => ''
                )
            ),
            'apply' => 0
        );
        $highLevel = array_merge($normalLevel, array(
            'rule_name' => Mage::helper('inventorysuccess')->__('High level low stock notifications'),
            'lowstock_threshold' => 2,
            'description' => Mage::helper('inventorysuccess')->__('High level low stock notifications'),
        ));
        $ruleModel = Mage::getModel('inventorysuccess/lowStockNotification_rule');
        try {
            $ruleModel->addData($normalLevel)->setId(null)->save();
            $ruleModel->addData($highLevel)->setId(null)->save();
        }catch (\Exception $e){
            return $this;
        }
        return $this;
    }
}