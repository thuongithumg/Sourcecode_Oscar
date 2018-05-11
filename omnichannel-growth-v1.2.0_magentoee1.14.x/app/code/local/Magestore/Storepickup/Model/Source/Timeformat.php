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
 * Class Magestore_Storepickup_Model_Source_Timeformat
 */
class Magestore_Storepickup_Model_Source_Timeformat
{
    /**
     * @return array
     */
    public function toOptionArray()
	{
            return array(  
                array('value' => '24', 'label' => Mage::helper('storepickup')->__('24h')),
                array('value' => '12', 'label' => Mage::helper('storepickup')->__('12h')),
        );
    }
}