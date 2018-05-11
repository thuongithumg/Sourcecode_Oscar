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
 * Class Magestore_Storepickup_Model_Source_Systemunit
 */
class Magestore_Storepickup_Model_Source_Systemunit{
    /**
     * @return array
     */
    public function toOptionArray(){
	return array(
            0   => array(
                        'value'=> 'km',
                        'label' => Mage::helper('storepickup')->__('Kilometers')
                    ),
            1   => array(
                        'value'=> 'mi',
                        'label' => Mage::helper('storepickup')->__('Miles')
                    ),
            2   => array(
                        'value'=> 'm',
                        'label' => Mage::helper('storepickup')->__('Meters')
                    ),
        );
    }
}