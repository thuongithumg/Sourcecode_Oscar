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
 * Class Magestore_Storepickup_Block_Displayallstores
 */
class Magestore_Storepickup_Block_Displayallstores extends Mage_Core_Block_Template
{
    /**
     *
     */
    public function addTopLinkStores()
	{
		$storeID = Mage::app()->getStore()->getId();
		if(Mage::getStoreConfig('carriers/storepickup/display_allstores',$storeID)==2) {
			$toplinkBlock = $this->getParentBlock();
			if($toplinkBlock)
			$toplinkBlock->addLink($this->__('Our Stores'),'storepickup/index/index','Our Stores',true,array(),10);
		}
	}

    /**
     *
     */
    public function addFooterLinkStores()
	{
		$storeID = Mage::app()->getStore()->getId();
		if(Mage::getStoreConfig('carriers/storepickup/display_allstores',$storeID)==1) {
			$footerBlock = $this->getParentBlock();
			if($footerBlock)
			$footerBlock->addLink($this->__('Our Stores'),'storepickup/index/index','Our Stores',true,array());
		}
	}
}
