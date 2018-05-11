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
 * Giftvoucher Api V2 model
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Magestore_Giftvoucher_Model_Api_V2 extends Magestore_Giftvoucher_Model_Api
{

    /**
     * Prepare data to create/massCreate/update/sendEmail.
     * Creating array for stdClass Object (using API v2)
     *
     * @param stdClass $data
     * @return array
     */
    protected function _prepareData($data)
    {
        if (null !== ($_data = get_object_vars($data))) {
            return parent::_prepareData($_data);
        }
        return array();
    }

    /**
     * Rewrite get list items (use for API v2)
     * 
     * @param stdClass $filters
     * @return array
     */
    public function items($filters = null)
    {
        $collection = Mage::getModel('giftvoucher/giftvoucher')->getCollection();

        $preparedFilters = array();
        if (isset($filters->filter) && is_array($filters->filter)) {
            foreach ($filters->filter as $_key => $_filter) {
                if (is_object($_filter) && isset($_filter->key) && isset($_filter->value)) {
                    $preparedFilters[$_filter->key] = $_filter->value;
                } else {
                    $preparedFilters[$_key] = $_filter;
                }
            }
        }
        if (isset($filters->complex_filter) && is_array($filters->complex_filter)) {
            foreach ($filters->complex_filter as $_key => $_filter) {
                if (Mage::getStoreConfig('api/config/compliance_wsi')) {
                    // WS-I compliance mode
                    if (is_object($_filter) && isset($_filter->key) && isset($_filter->value)) {
                        $preparedFilters[$_key] = array(
                            $_filter->key => $_filter->value
                        );
                    }
                } else if (is_object($_filter) && isset($_filter->key) && isset($_filter->value)) {
                    $_value = $_filter->value;
                    $preparedFilters[$_filter->key] = array(
                        $_value->key => $_value->value
                    );
                }
            }
        }

        if (!empty($preparedFilters)) {
            try {
                foreach ($preparedFilters as $field => $value) {
                    $collection->addFieldToFilter($field, $value);
                }
            } catch (Mage_Core_Exception $e) {
                $this->_fault('filters_invalid', $e->getMessage());
            }
        }

        $result = array();
        foreach ($collection as $giftvoucher) {
            $result[] = $giftvoucher->toArray();
        }
        return $result;
    }

}
