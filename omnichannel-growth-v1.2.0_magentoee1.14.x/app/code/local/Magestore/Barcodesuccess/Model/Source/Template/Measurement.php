<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 *   @category    Magestore
 *   @package     Magestore_Barcodesuccess
 *   @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Class Magestore_Barcodesuccess_Model_Source_Template_Measurement
 */
class Magestore_Barcodesuccess_Model_Source_Template_Measurement extends
    Varien_Object
{
    const MM      = 'mm';
    const CM      = 'cm';
    const IN      = 'in';
    const PX      = 'px';
    const PERCENT = '%';

    /**
     * Get options
     *
     * @return array
     */
    public static function toOptionArray()
    {
        $availableOptions = self::toOptionHash();
        $options          = array();
        foreach ( $availableOptions as $key => $value ) {
            $options[] = array(
                'label' => $value,
                'value' => $key,
            );  
        }
        return $options;
    }

    /**
     * @return array
     */
    public static function toOptionHash()
    {
        return array(
            self::MM      => Mage::helper('barcodesuccess')->__('mm'),
            self::CM      => Mage::helper('barcodesuccess')->__('cm'),
            self::IN      => Mage::helper('barcodesuccess')->__('in'),
            self::PX      => Mage::helper('barcodesuccess')->__('px'),
            self::PERCENT => Mage::helper('barcodesuccess')->__('%'),
        );
    }
}

