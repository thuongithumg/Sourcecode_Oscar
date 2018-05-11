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
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Adjuststock Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_Importexport_ImportproductController extends Mage_Adminhtml_Controller_Action
{
    /**
     * download sample file
     */
    public function downloadAction()
    {
        $importProductService = Magestore_Coresuccess_Model_Service::importProductService();
        $prepareData = $importProductService->getPrepareDataToDownload();
        $outputFile = "catalog_product_with_warehouse". date('Ymd_His').".csv";
        $this->_prepareDownloadResponse(
            $outputFile,
            $prepareData
        );
    }
    
    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('inventorysuccess/stockcontrol');
    }    
}