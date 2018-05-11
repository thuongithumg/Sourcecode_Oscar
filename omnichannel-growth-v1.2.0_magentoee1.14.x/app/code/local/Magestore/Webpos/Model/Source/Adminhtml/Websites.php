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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

class Magestore_Webpos_Model_Source_Adminhtml_Websites extends Varien_Object {

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $array = array('' => Mage::helper('webpos')->__('Default Website'));
        $websites = Mage::app()->getWebsites();
        if (count($websites) > 1){
            foreach ($websites as $website){
                $array[$website->getCode()] = $website->getName();
            }
        }
        $options = array();
        foreach ($array as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }
        return $options;
    }

    /**
     * @return array
     */
    public function getOptionArray()
    {
        $array = array('' => Mage::helper('webpos')->__('Default Website'));
        $websites = Mage::app()->getWebsites();
        if (count($websites) > 1){
            foreach ($websites as $website){
                $array[$website->getCode()] = $website->getName();
            }
        }
        return $array;
    }
}
