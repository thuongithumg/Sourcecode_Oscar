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
 * Adjuststock Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Service_Warehouse_OptionService
{
    /**
     * get model option as array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $optionArray = array();
        $collection = Mage::getModel('inventorysuccess/warehouse')->getCollection();
        $items = $collection->toArray(array(
            Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_ID,
            Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_NAME,
            Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_CODE,
        ));

        if(isset($items['items']) && count($items['items'])) {
            foreach($items['items'] as $item){
                $optionArray[$item[Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_ID]] = $this->_getWarehouseLabel($item);
            }
        }
        return $optionArray;
    }

    /**
     *
     * @param array $record
     * @return string
     */
    public function _getWarehouseLabel($record)
    {
        return $record[Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_NAME] .
                ' ('. $record[Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_CODE] .')';
    }

    /**
     * get model option hash as array
     *
     * @return array
     */
    public function getOptionHash()
    {
        $options = array();
        foreach ($this->getOptionArray() as $value => $label) {
            $options[] = array(
                'value'    => $value,
                'label'    => $label
            );
        }
        return $options;
    }
}