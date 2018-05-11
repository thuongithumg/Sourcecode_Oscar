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
 * Class Magestore_Inventorysuccess_Block_Adminhtml_Adjuststock_Sample
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Installation_Process extends Magestore_Coresuccess_Block_Adminhtml_Process_Abstract
{
    
    /**
     * 
     * @return Magestore_Coresuccess_Model_Service_Process_ProcessServiceInterface
     */
    public function getProcessService()
    {
        return Magestore_Coresuccess_Model_Service::installService();
    }
    
    /**
     * 
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getUrl('*/inventorysuccess_managestock/index');
    }
    
    /**
     * 
     * @return string
     */
    public function getHeaderText()
    {
        return $this->__('Scanning Magento Data');
    }

    /**
     * 
     * @return string
     */
    public function getRedirectMessage()
    {
        return $this->__('Redirecting to Stock listing page...');
    }    
}