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
 * Suppliersuccess Grid Block
 * 
 * @category    Magestore
 * @package     Magestore_Suppliersuccess
 * @author      Magestore Developer
 */
class Magestore_Suppliersuccess_Block_Adminhtml_Pricelist_Js extends Mage_Adminhtml_Block_Template
{
    /**
     * 
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('suppliersuccess/pricelist/js.phtml');
    }
    
    /**
     * 
     * @return string
     */
    public function getMassUpdateUrl()
    {
        return $this->getUrl('*/*/massupdate');
    }
    
    /**
     * 
     * @return string
     */
    public function getMassDeleteUrl()
    {
        return $this->getUrl('*/*/massdelete');
    }

}