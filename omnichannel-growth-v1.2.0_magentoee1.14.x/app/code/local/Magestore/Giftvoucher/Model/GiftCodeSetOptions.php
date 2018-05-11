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
 * Giftvoucher View Block
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */

class Magestore_Giftvoucher_Model_GiftCodeSetOptions extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    /**
     * Get Gift Card available templates
     *
     * @return array
     */
    public function getAvailableGiftcodeSets()
    {
        $giftcodeSets = Mage::getModel('giftvoucher/giftcodeset')->getCollection();

//            ->addFieldToFilter('status', '2');
        $listGiftcodeSets = array();
        foreach ($giftcodeSets as $giftcodeSet) {
            $listGiftcodeSets[] = array('label' => $giftcodeSet->getSetName(),
                'value' => $giftcodeSet->getSetId());
        }
        return  $listGiftcodeSets;
    }

    /**
     * Get model option as array
     *
     * @param bool $withEmpty
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        if (is_null($this->_options)) {
            $this->_options = $this->getAvailableGiftcodeSets();
        }
        $options = $this->_options;
        if ($withEmpty) {
            array_unshift($options, array(
                'value' => '',
                'label' => Mage::helper('giftvoucher')->__('-- Please Select --'),
            ));
        }
        return $options;
    }

}
