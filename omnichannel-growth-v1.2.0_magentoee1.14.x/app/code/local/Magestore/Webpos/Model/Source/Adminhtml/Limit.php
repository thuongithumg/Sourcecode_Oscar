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

class Magestore_Webpos_Model_Source_Adminhtml_Limit extends Varien_Object
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('label' => Mage::helper('webpos')->__('Last week'), 'value' => 7),
            array('label' => Mage::helper('webpos')->__('Last month'), 'value' => 30),
            array('label' => Mage::helper('webpos')->__('Last 3 months'), 'value' => 90),
            array('label' => Mage::helper('webpos')->__('Last 6 months'), 'value' => 180),
            array('label' => Mage::helper('webpos')->__('Last year'), 'value' => 365),
        );
    }

}