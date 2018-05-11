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
class Magestore_Barcodesuccess_Adminhtml_Barcodesuccess_Barcode_PrintController extends
    Mage_Adminhtml_Controller_Action
{

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/barcodesuccess');
    }   

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('barcodesuccess/print')
             ->_title(Mage::helper('barcodesuccess')->__('Print Barcode'));
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('barcodesuccess/adminhtml_barcode_print_edit'))
             ->_addLeft($this->getLayout()->createBlock('barcodesuccess/adminhtml_barcode_print_edit_tabs'));
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
     * @return Mage_Core_Controller_Varien_Action
     */
    public function gridAction()
    {
        return $this->loadLayout()->renderLayout();
    }

    /**
     * @return Zend_Controller_Response_Abstract
     */
    public function loadPrintPreviewAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $templateId        = $this->getRequest()->getParam('templateId');
        $template          = Mage::getModel('barcodesuccess/template')->load($templateId);
        $row               = $template->getData('label_per_row') ? $template->getData('label_per_row') : 1;
        $barcodeCollection = Mage::getModel('barcodesuccess/barcode')->getCollection()
                                 ->setCurPage(1)->setPageSize($row);
        $barcodeData       = array();
        foreach ( $barcodeCollection as $barcode ) {
            $barcodeData[] = $barcode->getData();
        }
        $data = array();
        if ( $barcode->getId() ) {
            $data['content'] = Mage::getBlockSingleton('barcodesuccess/barcode_template')
                                   ->setTemplateData($template->getData())
                                   ->setBarcodes($barcodeData)
                                   ->toHtml();
        }
        return $this->getResponse()->appendBody(json_encode($data));
    }

    /**
     * @return Zend_Controller_Response_Abstract
     */
    public function printConfigAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $barcodes          = $this->getRequest()->getParam('barcodes');
        $barcodes          = Mage::helper('adminhtml/js')->decodeGridSerializedInput($barcodes);
        $barcodeCollection = Mage::getModel('barcodesuccess/barcode')->getCollection()
                                 ->addFieldToFilter(Magestore_Barcodesuccess_Model_Barcode::BARCODE_ID, array('in' => array_keys($barcodes)));
        $barcodeData       = array();
        foreach ( $barcodeCollection as $barcode ) {
            $data          = $barcode->getData();
            $barcodeId     = $barcode->getId();
            $data['qty']   = $barcodes[$barcodeId]['qty'];
            $barcodeData[] = $data;
        }
        $templateId      = $this->getRequest()->getParam('templateId');
        $template        = Mage::getModel('barcodesuccess/template')->load($templateId);
        $data            = array();
        $data['content'] = Mage::getBlockSingleton('barcodesuccess/barcode_template')
                               ->setTemplateData($template->getData())
                               ->setBarcodes($barcodeData)
                               ->toHtml();
        return $this->getResponse()->appendBody(json_encode($data));
    }

}
