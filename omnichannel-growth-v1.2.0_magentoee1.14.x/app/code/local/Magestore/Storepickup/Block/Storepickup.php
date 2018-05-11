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
 * Class Magestore_Storepickup_Block_Storepickup
 */
class Magestore_Storepickup_Block_Storepickup extends Mage_Core_Block_Template
{
    /**
     * Magestore_Storepickup_Block_Storepickup constructor.
     */
    public function __construct()
	{	
		parent::__construct();
		
		$this->setData('shipping_model',Mage::getModel('storepickup/shipping_storepickup'));
	}

    /**
     * @return mixed
     */
    public function _prepareLayout()
    {
		$return = parent::_prepareLayout();
		
		$listStore = $this->getStoreByLocation();
			
		$this->setListStoreLocation($listStore);
		
		$this->setTemplate('storepickup/storepickup.phtml');
		
		return $return;
	}

    /**
     * @return mixed
     */
    public function getListTime()
	{
		return Mage::helper('storepickup')->getListTime();		
	}

    /**
     * @return bool
     */
    public function has_stores()
	{
		return true;
	}

    /**
     * @return mixed
     */
    public function getStoreByLocation()
	{
		
		if(! $this->hasData('storecollection'))
		{
			if($this->getShippingModel()->getConfigData('active_gapi'))	
			{
				$stores =  Mage::getSingleton('storepickup/store')->filterStoresUseGAPI();
			} else {
				$stores =  Mage::getSingleton('storepickup/store')->convertToList();
			}
			$this->setData('storecollection',$stores);
		}
		return $this->getData('storecollection');
	}

    /**
     * @return mixed
     */
    public function getAllStores()
	{
		return $collection = Mage::getModel('storepickup/store')->getCollection()
							->addFieldToFilter('status',1);
	}

    /**
     * @return mixed
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }	
}