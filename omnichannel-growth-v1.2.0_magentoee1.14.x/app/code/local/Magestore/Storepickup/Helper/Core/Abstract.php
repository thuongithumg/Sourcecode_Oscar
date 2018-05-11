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
 * Rewrite Abstract helper
 *
 * @author      Magestore team
 */
class Magestore_Storepickup_Helper_Core_Abstract extends Mage_Core_Helper_Data
{
   
    /**
     * Escape html entities
     *
     * @param   mixed $data
     * @param   array $allowedTags
     * @return  mixed
     */
    public function escapeHtml($data, $allowedTags = null)
    {
		$title = Mage::getStoreConfig('carriers/storepickup/title',Mage::app()->getStore()->getStoreId());
        $checkImageTag = strpos($data,'maps.google.com');
        $checkBR = strpos($data,$title.' - Free') ;        
        if ($checkBR || $checkImageTag) {    
            return $data;
        }
        return parent::escapeHtml($data, $allowedTags);
    }

    
}
