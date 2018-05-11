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
class Magestore_Barcodesuccess_Adminhtml_Barcodesuccess_ProductController extends
    Mage_Adminhtml_Controller_Action
{
    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function rendertabAction()
    {
        return $this->loadLayout()->renderLayout();
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function productbarcodegridAction()
    {
        return $this->loadLayout()->renderLayout();
    }

    /**
     * print 1 barcode in product edit tab
     * @return Zend_Controller_Response_Abstract
     */
    public function printBarcodeItemAction()
    {
        $barcodeId   = $this->getRequest()->getParam('barcodeId');
        $barcode     = Mage::getModel('barcodesuccess/barcode')->load($barcodeId);
        $printQty    = $this->getRequest()->getParam('printQty') ? $this->getRequest()->getParam('printQty') : 1;
        $barcodeData = array();
        for ( $i = 0; $i < $printQty; $i++ ) {
            $barcodeData[] = $barcode->getData();
        }
        $templateId = $this->getRequest()->getParam('templateId');
        $template   = Mage::getModel('barcodesuccess/template')->load($templateId);
        $data       = array();
        if ( $barcode->getId() ) {
            $data['content'] = Mage::getBlockSingleton('barcodesuccess/barcode_template')
                                   ->setTemplateData($template->getData())
                                   ->setBarcodes($barcodeData)
                                   ->toHtml();
        }
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        return $this->getResponse()->appendBody(json_encode($data));
    }

    /**
     * print all selected barcode in product edit tab
     * @return Zend_Controller_Response_Abstract
     */
    public function printSelectedBarcodeAction()
    {
        $barcodes    = Mage::helper('adminhtml/js')->decodeGridSerializedInput($this->getRequest()->getParam('barcodes'));
        $barcodeData = array();
        foreach ( $barcodes as $barcode ) {
            $barcode['print_qty'] = $barcode['print_qty'] ? $barcode['print_qty'] : 1;
            $model                = Mage::getModel('barcodesuccess/barcode')->load($barcode['barcode_id']);
            for ( $i = 0; $i < $barcode['print_qty']; $i++ ) {
                $barcodeData[] = $model->getData();
            }
        }
        $templateId = $this->getRequest()->getParam('templateId');
        $template   = Mage::getModel('barcodesuccess/template')->load($templateId);
        $data       = array(
            'content' => Mage::getBlockSingleton('barcodesuccess/barcode_template')
                             ->setTemplateData($template->getData())
                             ->setBarcodes($barcodeData)
                             ->toHtml(),
        );
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        return $this->getResponse()->appendBody(json_encode($data));
    }

    /**
     * @return Zend_Controller_Response_Abstract
     */
    public function deleteSelectedBarcodeAction()
    {
        $barcodes = Mage::helper('adminhtml/js')->decodeGridSerializedInput($this->getRequest()->getParam('barcodes'));
        $count    = 0;
        foreach ( $barcodes as $barcode ) {
            $model = Mage::getModel('barcodesuccess/barcode')->load($barcode['barcode_id']);
            if ( $model->getId() ) {
                try {
                    $model->delete();
                    $count++;
                } catch ( \Exception $e ) {
                }
            }
        }
        $data = array(
            'content' => Mage::helper('barcodesuccess')->__('Total of %d record(s) has been successfully deleted.', $count),
        );
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        return $this->getResponse()->appendBody(json_encode($data));
    }
    
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/barcodesuccess');
    }    
}
