<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Barcodesuccess Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @author      Magestore Developer
 */
class Magestore_Barcodesuccess_Adminhtml_Barcodesuccess_Barcode_ScanController extends
    Mage_Adminhtml_Controller_Action
{

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/barcodesuccess');
    }   

    /******* SCAN BARCODE *******/

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('barcodesuccess/scan')
             ->_title(Mage::helper('barcodesuccess')->__('Scan Barcode'));
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('barcodesuccess/adminhtml_barcode_scan_edit'))
             ->_addLeft($this->getLayout()->createBlock('barcodesuccess/adminhtml_barcode_scan_edit_tabs'));
        $this->renderLayout();
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function renderAction()
    {
        return $this->loadLayout()->renderLayout();
    }

    /**
     *
     */
    public function loadBarcodeAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $barcode = $this->getRequest()->getParam('barcode');
        $barcode = Mage::getModel('barcodesuccess/barcode')->load($barcode, Magestore_Barcodesuccess_Model_Barcode::BARCODE);
        $data    = array();
        if ( $barcode->getId() ) {
            $data                 = $barcode->getData();
            $product              = Mage::getModel('catalog/product')->load($barcode->getProductId());
            $data['thumbnail']    = Magestore_Coresuccess_Model_Service::barcodeProductService()->getThumbnailHtml($product);
            $data['price']        = $product->getPrice();
            $data['name']         = $product->getName();
            $data['qty']          = Magestore_Coresuccess_Model_Service::barcodeProductService()->getQtyHtml($product);
            $data['availability'] = Magestore_Coresuccess_Model_Service::barcodeProductService()->getAvailabilityText($product);
            $data['status']       = Magestore_Coresuccess_Model_Service::barcodeProductService()->getStatusText($product);
            $data['detail']       = Magestore_Coresuccess_Model_Service::barcodeProductService()->getDetailUrlHtml($product);
        }
        $this->getResponse()->appendBody(json_encode($data));
    }

    /**
     *
     */
    public function printAction()
    {
        $barcode     = $this->getRequest()->getParam('barcode');
        $templateId  = $this->getRequest()->getParam('templateId');
        $barcode     = Mage::getModel('barcodesuccess/barcode')->load($barcode, Magestore_Barcodesuccess_Model_Barcode::BARCODE);
        $template    = Mage::getModel('barcodesuccess/template')->load($templateId);
        $barcodeData = array();
        for ( $i = 0; $i < $this->getRequest()->getParam('printQty'); $i++ ) {
            $barcodeData[] = $barcode->getData();
        }
        $data = array();
        if ( $barcode->getId() ) {
            $data['content'] = Mage::getBlockSingleton('barcodesuccess/barcode_template')
                                   ->setTemplateData($template->getData())
                                   ->setBarcodes($barcodeData)
                                   ->toHtml();
        }
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $this->getResponse()->appendBody(json_encode($data));
    }

}
