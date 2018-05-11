<?php
/**
 * Created by PhpStorm.
 * User: duongdiep
 * Date: 06/02/2017
 * Time: 15:59
 */


/**
 * Class Magestore_Barcodesuccess_Model_Source_GenerateType
 */
class Magestore_Barcodesuccess_Model_Source_Template_Rotate extends
    Varien_Object
{
    const Rotate_90 = '90';
    const Rotate_360 = '0';
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
            self::Rotate_360       => Mage::helper('barcodesuccess')->__('0 degree'),
            self::Rotate_90 => Mage::helper('barcodesuccess')->__('90 degrees'),
        );
    }
}
