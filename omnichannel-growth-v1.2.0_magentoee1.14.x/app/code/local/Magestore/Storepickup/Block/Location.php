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
 * Class Magestore_Storepickup_Block_Location
 */
class Magestore_Storepickup_Block_Location extends Mage_Core_Block_Template
{
    /**
     * @return mixed
     */
    public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getListCountry()
	{
		return Mage::helper('storepickup')->getListCountry();
	}

    /**
     * @return mixed
     */
    public function getListRegion()
	{
		return Mage::helper('storepickup/location')->getListRegion();
	}

    /**
     * @return null
     */
    public function getListCity()
	{
		if($this->getCurrRegionId())
			return Mage::helper('storepickup/location')->getListCity($this->getCurrRegionId());
		else
			return null;
	}

    /**
     * @return null
     */
    public function getListSuburb()
	{
		if($this->getCurrCityId())
			return Mage::helper('storepickup/location')->getListSuburb($this->getCurrCityId());
		else
			return null;
	}

    /**
     * @return mixed
     */
    public function getCurrCountryId()
	{
		if(!$this->hasData('country_id'))
		{
			if($this->_getShippingAddress())
			{
				$shippingAddress = $this->_getShippingAddress();
			
				if($shippingAddress->getCountryId());
					$this->setData('country_id',$shippingAddress->getCountryId());
			} 
		}	
		return $this->getData('country_id');
	}

    /**
     * @return mixed
     */
    public function getCurrState()
	{
		if(!$this->hasData('state'))
		{
			if($this->_getShippingAddress())
			{
				$shippingAddress = $this->_getShippingAddress();
				if($shippingAddress->getState())
				{
					$this->setData('state',$shippingAddress->getState());
				} 
				else {
					$collection = Mage::getResourceModel('storepickup/store_collection')
							->addFieldToFilter('state',$shippingAddress->getState());
					if(count($collection))
					{
						foreach($collection as $item){}
						$this->setData('state',$item->getState());
					}
				}
			} 
		}	
		return $this->getData('state');
	}

    /**
     * @return mixed
     */
    public function getCurrCityId()
	{
		if(!$this->hasData('city_id'))
		{
			if($this->_getShippingAddress())
			{
				$shippingAddress = $this->_getShippingAddress();
				$collection = Mage::getResourceModel('storepickup/store')
						->addFieldToFilter('city',$shippingAddress->getCity());
				if(count($collection))
				{
					foreach($collection as $item){}
					$this->setData('city_id',$item->getCityId());
				}
			} 
		}	
		return $this->getData('city_id');
	}

    /**
     * @return null
     */
    public function getCurrSuburbId()
	{
		return null;
	}

    /**
     * @return mixed
     */
    protected function _getShippingAddress()
	{
		if(! $this->hasData('shippingaddress'))
		{
			$shippingAddress = Mage::getSingleton('checkout/cart')
								->getQuote()
								->getShippingAddress();
			$this->setData('shippingaddress',$shippingAddress);
		}	
		
		return $this->getData('shippingaddress');
	}
}