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
 * Giftvoucher Designpattern Model
 * 
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @author      Magestore Developer
 */

class Magestore_Giftvoucher_Model_Designpattern extends Varien_Object
{
    const PATTERN_LEFT = 1;
    const PATTERN_TOP = 2;
    const PATTERN_CENTER = 3;
    const PATTERN_SIMPLE = 4;
    const PATTERN_AMAZON = '5';

    /**
     * Get model option as array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        $options = array();
        $optionsArray = self::getOptions();
        foreach ($optionsArray as $option) {
            $options[$option['value']] = $option['label'];
        }

        return $options;
    }

    /**
     * @return array
     */
    static public function getOptions()
    {
        $options = array();
        $allFiles = Mage::helper('giftvoucher')->getAllTemplatePatternId();
        foreach ($allFiles as $file) {
            $options[] = array(
                'value' => $file,
                'label' => $file.'.html'
            );
        }
        return $options;
    }

    /**
     * @return array
     */
    static public function getOnlyNewTemplate()
    {
        return array(
            array(
                'value' => self::PATTERN_AMAZON,
                'label' => self::PATTERN_AMAZON.'.html'
            )
        );
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return self::getOptions();
    }

}
