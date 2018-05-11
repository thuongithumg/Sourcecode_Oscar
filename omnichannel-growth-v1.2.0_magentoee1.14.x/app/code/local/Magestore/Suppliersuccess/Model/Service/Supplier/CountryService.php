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
 * @package     Magestore_Suppliersuccess
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Suppliersuccess Model
 * 
 * @category    Magestore
 * @package     Magestore_Suppliersuccess
 * @author      Magestore Developer
 */
class Magestore_Suppliersuccess_Model_Service_Supplier_CountryService
{
    
    /**
     * 
     * @return array
     */
    public function getCountryList()
    {
        $result = array();
        $collection = Mage::getModel('directory/country')->getCollection();
        foreach ($collection as $country) {
            $cid = $country->getId();
            $cname = $country->getName();
            $result[$cid] = $cname;
        }
        return $result;
    }

    /**
     * 
     * @return array
     */
    public function getCountryListHash()
    {
        $options = array('' => Mage::helper('suppliersuccess')->__('--Please Select--'));
        foreach ($this->getCountryList() as $value => $label) {
            if ($label)
                $options[] = array(
                    'value' => $value,
                    'label' => $label
                );
        }
        return $options;
    }

}
