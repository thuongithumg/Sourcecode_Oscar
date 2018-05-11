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
 * @package     Magestore_RewardPointsApi
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardPointsApi Customer Api Model
 * 
 * @category    Magestore
 * @package     Magestore_RewardPointsApi
 * @author      Magestore Developer
 */
class Magestore_RewardPointsApi_Model_Customer_Api_V2 extends Magestore_RewardPointsApi_Model_Customer_Api {

    protected function _prepareData($data)
    {
        if (null !== ($_data = get_object_vars($data))) {
            return parent::_prepareData($_data);
        }
        return parent::_prepareData($data);
    }    

}

// Class Mage_Sales_Model_Order_Api End