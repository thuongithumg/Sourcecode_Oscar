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
 * Giftvoucher Typeofbarcode Model
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */

class Magestore_Giftvoucher_Model_Typeofbarcode
{

    /**
     * Get model option as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $positions = array(
            'code128' => Mage::helper('giftvoucher')->__('Code 128'),
            'qr' => Mage::helper('giftvoucher')->__('QR code'),
        );
        $options = array();
        foreach ($positions as $code => $label) {
            $options[] = array(
                'value' => $code,
                'label' => $label
            );
        }
        return $options;
    }

}
