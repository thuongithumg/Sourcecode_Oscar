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
class Magestore_Barcodesuccess_Adminhtml_Barcodesuccess_ScanController extends
    Mage_Adminhtml_Controller_Action
{

    /**
     * @return Zend_Controller_Response_Abstract
     */
    public function loadBarcodeAction()
    {
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        $barcode = $this->getRequest()->getParam('barcode');
        $barcode = Mage::getModel('barcodesuccess/barcode')->load($barcode, Magestore_Barcodesuccess_Model_Barcode::BARCODE);
        $data    = array();
        if ( $barcode->getId() ) {
            $data                 = $barcode->getData();
            $product              = Mage::getModel('catalog/product')->getCollection()
                                        ->addAttributeToSelect('name')
                                        ->addFieldToFilter('entity_id', $barcode->getProductId())
                                        ->getFirstItem();
            $data['product_name'] = $product->getName();
        }
        return $this->getResponse()->appendBody(json_encode($data));
    }

    /**
     * @return Zend_Controller_Response_Abstract
     */
    public function submitBarcodeAction()
    {
        $barcodes = json_decode($this->getRequest()->getParam('barcodes'), true);
        $this->_getSession()->setData('scan_barcodes', $barcodes);
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        return $this->getResponse()->appendBody(json_encode(array()));
    }
    
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/barcodesuccess');
    }    
}
