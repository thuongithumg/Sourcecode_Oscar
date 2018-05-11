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
 * @package     Magestore_Suppliersuccess
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Suppliersuccess Model
 * 
 * @category    Magestore
 * @package     Magestore_Suppliersuccess
 * @author      Magestore Developer
 */
class Magestore_Suppliersuccess_Model_Supplier 
    extends Mage_Core_Model_Abstract
    implements Magestore_Coresuccess_Model_Service_ProductSelection_SelectionInterface
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('suppliersuccess/supplier');
    }
    
    /**
     * get selection-product model
     * 
     * @return Magestore_Coresuccess_Model_Service_ProductSelection_SelectionProductInterface
     */
    public function getSelectionProductModel()
    {
        return Mage::getModel('suppliersuccess/supplier_product');
    }
}