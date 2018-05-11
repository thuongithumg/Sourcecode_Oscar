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
 * Class Magestore_Storepickup_Block_Adminhtml_Sales_Order_Create_Shipping_Method_Form
 */
class Magestore_Storepickup_Block_Adminhtml_Sales_Order_Create_Shipping_Method_Form extends Mage_Adminhtml_Block_Sales_Order_Create_Shipping_Method_Form
{
    /**
     * @return mixed
     */
    protected function _toHtml(){
		$this->setTemplate('storepickup/form.phtml');
		return parent::_toHtml();
	}
	
	//(6-2-2013)
    /**
     * @param $store
     * @return mixed
     */
    public function getCoordinates($store)
	{		
		$address['street'] = $store->getSuburb();
		$address['street'] = '';
		$address['city'] = $store->getCity();
		$address['region'] = $store->getRegion();
		$address['zipcode'] = $store->getZipcode();
		$address['country'] = $store->getCountryName();
		
		$coordinates = Mage::getModel('storepickup/gmap')
							->getCoordinates($address);
		if(! $coordinates)
		{	
			$coordinates['lat'] = '0.000';
			$coordinates['lng'] = '0.000';			
		}

		return $coordinates;
	}
}