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
 * Class Magestore_Barcodesuccess_Model_Source_GenerateType
 */
class Magestore_Barcodesuccess_Model_Source_Template_Type extends
    Varien_Object
{
    const TYPE_STANDARD = 'standard';
    const TYPE_A4       = 'a4';
    const TYPE_JEWELRY  = 'jewelry';

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
            self::TYPE_STANDARD => Mage::helper('barcodesuccess')->__('Standard'),
            self::TYPE_A4       => Mage::helper('barcodesuccess')->__('A4'),
            self::TYPE_JEWELRY  => Mage::helper('barcodesuccess')->__('Jewelry'),
        );
    }

    /**
     * @return array
     */
    protected function getDefaultStandard()
    {
        $data                     = array();
        $data["type"]             = self::TYPE_STANDARD;
        $data["status"]           = Magestore_Barcodesuccess_Model_Source_Template_Status::ACTIVE;
        $data["measurement_unit"] = Magestore_Barcodesuccess_Model_Source_Template_Measurement::MM;
        $data["symbology"]        = Magestore_Barcodesuccess_Model_Source_Template_Symbology::CODE_128;
        $data["name"]             = Mage::helper('barcodesuccess')->__("Standard");
        $data["label_per_row"]    = "3";
        $data["paper_width"]      = "109";
        $data["paper_height"]     = "24";
        $data["label_width"]      = "35";
        $data["label_height"]     = "22";
        $data["font_size"]        = "16";
        $data["top_margin"]       = "1";
        $data["left_margin"]      = "1";
        $data["bottom_margin"]    = "1";
        $data["right_margin"]     = "1";
        return $data;
    }

    /**
     * @return array
     */
    protected function getDefaultA4()
    {
        $data                     = array();
        $data["type"]             = self::TYPE_A4;
        $data["status"]           = Magestore_Barcodesuccess_Model_Source_Template_Status::ACTIVE;
        $data["measurement_unit"] = Magestore_Barcodesuccess_Model_Source_Template_Measurement::MM;
        $data["symbology"]        = Magestore_Barcodesuccess_Model_Source_Template_Symbology::CODE_128;
        $data["name"]             = Mage::helper('barcodesuccess')->__("A4");
        $data["label_per_row"]    = "4";
        $data["paper_width"]      = "210";
        $data["paper_height"]     = "20";
        $data["label_width"]      = "48.25";
        $data["label_height"]     = "16";
        $data["font_size"]        = "16";
        $data["top_margin"]       = "2";
        $data["left_margin"]      = "2";
        $data["bottom_margin"]    = "2";
        $data["right_margin"]     = "2";
        return $data;
    }

    /**
     * @return array
     */
    protected function getDefaultJewelry()
    {
        $data                     = array();
        $data["type"]             = self::TYPE_JEWELRY;
        $data["status"]           = Magestore_Barcodesuccess_Model_Source_Template_Status::ACTIVE;
        $data["measurement_unit"] = Magestore_Barcodesuccess_Model_Source_Template_Measurement::MM;
        $data["symbology"]        = Magestore_Barcodesuccess_Model_Source_Template_Symbology::CODE_128;
        $data["name"]             = Mage::helper('barcodesuccess')->__("Jewelry");
        $data["label_per_row"]    = "1";
        $data["paper_width"]      = "88";
        $data["paper_height"]     = "15";
        $data["label_width"]      = "25";
        $data["label_height"]     = "11";
        $data["font_size"]        = "24";
        $data["top_margin"]       = "1";
        $data["left_margin"]      = "1";
        $data["bottom_margin"]    = "1";
        $data["right_margin"]     = "1";
        return $data;
    }

    /**
     * @param string $type
     * @return array
     */
    public function getDefaultData( $type = "" )
    {
        switch ( $type ) {
            case self::TYPE_STANDARD:
                $data = $this->getDefaultStandard();
                break;
            case self::TYPE_A4:
                $data = $this->getDefaultA4();
                break;
            case self::TYPE_JEWELRY:
                $data = $this->getDefaultJewelry();
                break;
            default:
                $data                 = array();
                $data[self::TYPE_STANDARD] = $this->getDefaultStandard();
                $data[self::TYPE_A4]       = $this->getDefaultA4();
                $data[self::TYPE_JEWELRY]  = $this->getDefaultJewelry();
                break;
        }
        return $data;
    }
}

