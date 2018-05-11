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
 * @package     Magestore_Coresuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Coresuccess Model
 * 
 * @category    Magestore
 * @package     Magestore_Coresuccess
 * @author      Magestore Developer
 */
interface Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface
{
    /**
     * Get Entity Id
     * 
     * @return int
     */
    public function getId();
    
    /**
     * get selection-product model
     * 
     * @return Magestore_Coresuccess_Model_Service_ProductSelection_SelectionProductInterface
     */
    public function getSelectionProductModel();
    
    /**
     * Get resource model of stock activity object
     * 
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    public function getResource();    
}