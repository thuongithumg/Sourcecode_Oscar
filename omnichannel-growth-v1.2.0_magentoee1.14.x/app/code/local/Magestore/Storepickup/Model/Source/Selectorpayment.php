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
 * Class Magestore_Storepickup_Model_Source_Selectorpayment
 */
class Magestore_Storepickup_Model_Source_Selectorpayment
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value'=>0, 'label'=>Mage::helper('storepickup')->__('All Payment Methods')),
            array('value'=>1, 'label'=>Mage::helper('storepickup')->__('Only Selected Payment Methods')),
        );
    }
}
