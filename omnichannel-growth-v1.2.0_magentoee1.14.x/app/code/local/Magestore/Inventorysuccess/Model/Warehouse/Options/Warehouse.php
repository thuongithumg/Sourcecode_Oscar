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
 * Warehouse Options Warehouses Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Warehouse_Options_Warehouse extends Varien_Object
{
    /**
     * get model option as array
     *
     * @return array
     */
    public static function getOptionArray()
    {
        $collection = Mage::getResourceModel('inventorysuccess/warehouse_collection');
        $options = array();
        foreach ($collection as $warehouse){
            $options[$warehouse->getWarehouseId()] = $warehouse->getWarehouse();
        }
        return $options;
    }

    /**
     * get model option hash as array
     *
     * @return array
     */
    public static function getOptionHash()
    {
        $options = array();
        foreach (self::getOptionArray() as $value => $label) {
            $options[] = array(
                'value'    => $value,
                'label'    => $label
            );
        }
        return $options;
    }
}