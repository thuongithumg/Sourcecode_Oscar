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
 * Warehouse Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_SupplyNeeds extends Mage_Core_Model_Abstract
{

    /** supply needs' period */
    const SALES_PERIOD_LAST_7_DAYS = 'last_7_days';
    const SALES_PERIOD_LAST_30_DAYS = 'last_30_days';
    const SALES_PERIOD_3_MONTHS = 'last_3_months';
    const CUSTOM_RANGE = 'custom';

    /** term tables */
    const TEMP_WAREHOUSE_PRODUCTS = 'temp_warehouse_product';
    const TEMP_SHIPMENT_ITEM = 'temp_shipment_item';
    const TEMP_WAREHOUSE_SHIPMENT_ITEM = 'temp_warehouse_shipment_item';

    /** post fields */
    const WAREHOUSE_IDS = 'warehouse_ids';
    const SALES_PERIOD = 'sales_period';
    const FROM_DATE = 'from_date';
    const TO_DATE = 'to_date';
    const FORECAST_DATE_TO = 'forecast_date_to';
    /**
     * 
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('inventorysuccess/supplyNeeds');
    }

    /**
     * @return array
     */
    public function getSalesPeriod()
    {
        return array(
            self::SALES_PERIOD_LAST_7_DAYS => Mage::helper('inventorysuccess')->__('Last 7 days'),
            self::SALES_PERIOD_LAST_30_DAYS => Mage::helper('inventorysuccess')->__('Last 30 days'),
            self::SALES_PERIOD_3_MONTHS => Mage::helper('inventorysuccess')->__('Last 3 months'),
            self::CUSTOM_RANGE => Mage::helper('inventorysuccess')->__('Custom Range')
        );
    }
}