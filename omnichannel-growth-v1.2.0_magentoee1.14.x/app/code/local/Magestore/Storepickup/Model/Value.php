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
 * Class Magestore_Storepickup_Model_Value
 */
class Magestore_Storepickup_Model_Value extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('storepickup/value');
    }

    /**
     * @param $storePickupId
     * @param $storeId
     * @param $attributeCode
     * @return $this
     */
    public function loadAttributeValue($storePickupId, $storeId, $attributeCode) {
        $attributeValue = $this->getCollection()
    		->addFieldToFilter('storepickup_id', $storePickupId)
    		->addFieldToFilter('store_id', $storeId)
    		->addFieldToFilter('attribute_code',$attributeCode)
    		->getFirstItem();
		$this->setData('storepickup_id', $storePickupId)
			->setData('store_id',$storeId)
			->setData('attribute_code',$attributeCode);
    	if ($attributeValue)
    		$this->addData($attributeValue->getData())
    			->setId($attributeValue->getId());
		return $this;
    }
}