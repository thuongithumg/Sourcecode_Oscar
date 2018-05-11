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
 * Class Magestore_Storepickup_Block_Adminhtml_Sales_Tab_Storepickup
 */
class Magestore_Storepickup_Block_Adminhtml_Sales_Tab_Storepickup
		extends Mage_Adminhtml_Block_Widget_Form
		implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Magestore_Storepickup_Block_Adminhtml_Sales_Tab_Storepickup constructor.
     */
    public function __construct()
	{
		parent::__construct();
		$this->setTemplate('storepickup/storepickup.phtml');
	}

    /**
     * @return mixed
     */
    public function getTabLabel()	{
		return Mage::helper('sales')->__('Store Pickup');
	}

    /**
     * @return mixed
     */
    public function getTabTitle() {
		return Mage::helper('sales')->__('Store Pickup');
	}

    /**
     * @return bool
     */
    public function canShowTab()	{
		if($this->getStorepickup())	
			return true;
		else
			return false;
		}

    /**
     * @return bool
     */
    public function isHidden()	{
		if($this->getStorepickup())
			return false;
		else
			return true;
	}

    /**
     * @return mixed
     */
    public function getStorepickup()
	{
            
		if(!$this->hasData('storepickup'))
		{
			$storepickup = null;
			
			$order = $this->getOrder();
			
			if (!$order) 
			{
				$this->setData('storepickup',null);
				return $this->getData('storepickup');
			}
			
			$order_id = $order->getId();
			
			$storepickup = Mage::helper('storepickup')->getStorepickupByOrderId($order_id);
			$this->setData('storepickup',$storepickup);
		}
		return $this->getData('storepickup');
	}

    /**
     * @return mixed
     */
    /** @noinspection PhpInconsistentReturnPointsInspection */
    public function getOrder()
    {       
        if (Mage::registry('current_order')) {
            return Mage::registry('current_order');
        }
        if (Mage::registry('order')) {
            return Mage::registry('order');
        }
        return nulll;
    }

    /**
     * @param $order_id
     * @return null
     */
    public function getShippingTime($order_id)
	{		
		$time = null;
		if ($order_id) { 
			$storeorder = Mage::getModel('storepickup/storeorder')->getCollection()
						->addFieldToFilter('order_id',$order_id)
						->getFirstItem();
		}					
		if ($storeorder)
			$time = $storeorder->getShippingTime();	
		return 	$time;		
	}

    /**
     * @param $order_id
     * @return null
     */
    public function getShippingDate($order_id)
	{		
		$date = null;
		if ($order_id) {
			$storeorder = Mage::getModel('storepickup/storeorder')->getCollection()
						->addFieldToFilter('order_id',$order_id)
						->getFirstItem();
		}				
		if ($storeorder)
			$date = $storeorder->getShippingDate();
		return 	$date;		
	}	
}