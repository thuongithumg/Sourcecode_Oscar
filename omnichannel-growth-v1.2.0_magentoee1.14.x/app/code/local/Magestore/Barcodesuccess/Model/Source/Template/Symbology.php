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
 * Class Magestore_Barcodesuccess_Model_Source_Template_Symbology
 */
class Magestore_Barcodesuccess_Model_Source_Template_Symbology extends
    Varien_Object
{
    const CODE_128           = 'code128';
    const CODE_25            = 'code25';
    const CODE_25INTERLEAVED = 'code25interleaved';
    const CODE_39            = 'code39';
    const CODE_EAN13         = 'ean13';
    const CODE_INDENTCODE    = 'identcode';
    const CODE_ITF14         = 'itf14';
    const CODE_LEITCODE      = 'leitcode';
    const CODE_ROYALMAIL     = 'royalmail';

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
            self::CODE_128           => Mage::helper('barcodesuccess')->__('Code-128'),
            self::CODE_25            => Mage::helper('barcodesuccess')->__('Code-25'),
            self::CODE_25INTERLEAVED => Mage::helper('barcodesuccess')->__('Interleaved 2 of 5'),
            self::CODE_39            => Mage::helper('barcodesuccess')->__('Code-39'),
            self::CODE_EAN13         => Mage::helper('barcodesuccess')->__('Ean-13'),
            self::CODE_INDENTCODE    => Mage::helper('barcodesuccess')->__('Identcode'),
            self::CODE_ITF14         => Mage::helper('barcodesuccess')->__('Itf14'),
            self::CODE_LEITCODE      => Mage::helper('barcodesuccess')->__('Leitcode'),
            self::CODE_ROYALMAIL     => Mage::helper('barcodesuccess')->__('Royalmail'),
        );
    }
}

