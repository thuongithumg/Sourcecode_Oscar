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
class Magestore_Inventorysuccess_Model_Service_SupplyNeedsService
{

    public function getProductSupplyNeedsCollection($topFilter, $sort, $dir)
    {
        /** @var Magestore_Inventorysuccess_Model_Mysql4_SupplyNeeds $supplyNeedsResource */
        $supplyNeedsResource = Mage::getResourceModel('inventorysuccess/supplyNeeds');
        $collection = $supplyNeedsResource->getProductSupplyNeedsCollection($topFilter, $sort, $dir);
        return $collection;
    }

    /**
     * @param $salesPeriod
     * @return bool|string
     */
    public function getFromDateBySalePeriod($salesPeriod) {
        $fromDate ='';
        if ($salesPeriod == Magestore_Inventorysuccess_Model_SupplyNeeds::SALES_PERIOD_LAST_7_DAYS) {
            $fromDate = date('Y-m-d', strtotime('-7 days', strtotime(date('Y-m-d'))));
        }
        if ($salesPeriod == Magestore_Inventorysuccess_Model_SupplyNeeds::SALES_PERIOD_LAST_30_DAYS) {
            $fromDate = date('Y-m-d', strtotime('-30 days', strtotime(date('Y-m-d'))));
        }
        if ($salesPeriod == Magestore_Inventorysuccess_Model_SupplyNeeds::SALES_PERIOD_3_MONTHS) {
            $fromDate = date('Y-m-d', strtotime('-3 months', strtotime(date('Y-m-d'))));
        }
        return $fromDate;
    }

    /**
     * get post filed to top filter
     * @return array
     */
    public function getPostFields()
    {
        return array(
            Magestore_Inventorysuccess_Model_SupplyNeeds::WAREHOUSE_IDS,
            Magestore_Inventorysuccess_Model_SupplyNeeds::SALES_PERIOD,
            Magestore_Inventorysuccess_Model_SupplyNeeds::FROM_DATE,
            Magestore_Inventorysuccess_Model_SupplyNeeds::TO_DATE,
            Magestore_Inventorysuccess_Model_SupplyNeeds::FORECAST_DATE_TO
        );
    }
}