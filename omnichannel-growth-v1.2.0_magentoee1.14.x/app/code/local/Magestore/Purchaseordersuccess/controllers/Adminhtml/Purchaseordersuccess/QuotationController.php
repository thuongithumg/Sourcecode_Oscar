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

use Magestore_Purchaseordersuccess_Model_Purchaseorder_Options_Type as PurchaseorderType;

class Magestore_Purchaseordersuccess_Adminhtml_Purchaseordersuccess_QuotationController
    extends Magestore_Purchaseordersuccess_Controller_Action
{

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->_initAction()
            ->_setActiveMenu('purchaseordersuccess/quotation/view')
            ->_title($this->__('Quotations'))
            ->renderLayout();
    }

    /**
     * Grid action
     */
    public function gridAction()
    {
        $this->_initAction()
            ->renderLayout();
    }
    
    /**
     * Export quotation grid to csv file
     */
    public function exportCsvAction()
    {
        $fileName = 'quotation.csv';
        $content = $this->getLayout()->createBlock('purchaseordersuccess/adminhtml_quotation_grid')->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export quotation grid to xml file
     */
    public function exportXmlAction()
    {
        $fileName = 'quotation.xml';
        $content = $this->getLayout()->createBlock('purchaseordersuccess/adminhtml_quotation_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function newAction()
    {
        return $this->_forward('new', 'purchaseordersuccess_purchaseorder', null, array('type' => PurchaseorderType::TYPE_QUOTATION));
    }

    public function viewAction()
    {
        return $this->_forward(
            'view', 'purchaseordersuccess_purchaseorder', null, array('type' => PurchaseorderType::TYPE_QUOTATION)
        );
    }

    public function saveAction()
    {
        $this->_forward('save', 'purchaseordersuccess_purchaseorder');
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('purchaseordersuccess/quotation');
    }     
}