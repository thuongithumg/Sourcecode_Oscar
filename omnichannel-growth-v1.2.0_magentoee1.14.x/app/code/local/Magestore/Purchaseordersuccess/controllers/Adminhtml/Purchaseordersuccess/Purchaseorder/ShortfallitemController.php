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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseordersuccess Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */

class Magestore_Purchaseordersuccess_Adminhtml_Purchaseordersuccess_Purchaseorder_ShortfallitemController
    extends Magestore_Purchaseordersuccess_Controller_Action
{
    /**
     * Export purchase order grid to csv file
     */
    public function exportCsvAction()
    {
        $id = $this->getRequest()->getParam('id');
        $this->purchaseorderService->registerPurchaseOrder($id);
        $fileName = 'purchaseorder_shortfall_item.csv';
        $content = $this->getLayout()
            ->createBlock('purchaseordersuccess/adminhtml_purchaseorder_edit_tab_shortfallitem_grid')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export purchase order grid to xml file
     */
    public function exportXmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $this->purchaseorderService->registerPurchaseOrder($id);
        $fileName = 'purchaseorder_shortfall_item.xml';
        $content = $this->getLayout()
            ->createBlock('purchaseordersuccess/adminhtml_purchaseorder_edit_tab_shortfallitem_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
}