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
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Storepickup_Model_Source_Searchby
 */
class Magestore_Storepickup_Model_Source_Searchby{
    /**
     * @return array
     */
    public function toOptionArray(){
	return array(
            0   => array(
                        'value'=> 'country_name',
                        'label' => Mage::helper('storepickup')->__('Country')
                    ),
            1   => array(
                        'value'=> 'state',
                        'label' => Mage::helper('storepickup')->__('State')
                    ),
            2   => array(
                        'value'=> 'city',
                        'label' => Mage::helper('storepickup')->__('City')
                    ),
            3   => array(
                        'value'=> 'store_name',
                        'label' => Mage::helper('storepickup')->__('Store Name')
                    ),
            4   => array(
                        'value'=> 'zipcode',
                        'label' => Mage::helper('storepickup')->__('Zipcode')
                    ),
        );
    }
}