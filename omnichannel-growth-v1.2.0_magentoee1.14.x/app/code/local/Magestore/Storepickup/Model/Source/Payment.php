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
 * Class Magestore_Storepickup_Model_Source_Payment
 */
class Magestore_Storepickup_Model_Source_Payment
{
    /**
     * @return array|void
     */
    public function toOptionArray()
	{
		$collection = Mage::getModel('payment/config')->getActiveMethods();
		
		if(! count($collection))
			return array();
			
		$options = array();	
			
		foreach($collection as $item)
		{
			$title = $item->getTitle() ? $item->getTitle() : $item->getId();
			$options[] = array('value'=> $item->getId(), 'label' => $title);
		}
		
		return $options;
	}
}