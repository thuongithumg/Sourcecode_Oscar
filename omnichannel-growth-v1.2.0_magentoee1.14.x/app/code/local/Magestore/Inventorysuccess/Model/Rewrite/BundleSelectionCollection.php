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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventorysuccess Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */

class Magestore_Inventorysuccess_Model_Rewrite_BundleSelectionCollection
    extends Mage_Bundle_Model_Resource_Selection_Collection
{
    /**
     * Initialize collection select
     *
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        if(Mage::getSingleton('admin/session')->getUser()) {
            return $this;
        }        
        Magestore_Coresuccess_Model_Service::productLimitationService()->filterProductByCurrentStock($this);
    } 
}
