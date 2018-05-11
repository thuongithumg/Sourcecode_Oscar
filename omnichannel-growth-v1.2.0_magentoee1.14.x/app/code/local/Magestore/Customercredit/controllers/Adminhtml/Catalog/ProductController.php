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
 * @package     Magestore_Storecredit
 * @module      Storecredit
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Created by PhpStorm.
 * User: DuyHung
 * Date: 7/8/2016
 * Time: 11:50 AM
 */
//require_once("Mage/Adminhtml/controllers/Catalog/ProductController.php");
require_once(Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'Catalog/ProductController.php');

/**
 * Class Magestore_Customercredit_Adminhtml_Catalog_ProductController
 */
class Magestore_Customercredit_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
    public function saveAction()
    {
        $storeId = $this->getRequest()->getParam('store');
        $redirectBack = $this->getRequest()->getParam('back', false);
        $productId = $this->getRequest()->getParam('id');
        $isEdit = (int)($this->getRequest()->getParam('id') != null);
        $isBackPageCredit=$this->getRequest()->getParam('back_page_credit_product');
        $data = $this->getRequest()->getPost();
        if ($data) {
            $this->_filterStockData($data['product']['stock_data']);

            $product = $this->_initProductSave();

            try {
                $product->save();
                $productId = $product->getId();

                if (isset($data['copy_to_stores'])) {
                    $this->_copyAttributesBetweenStores($data['copy_to_stores'], $product);
                }

                $this->_getSession()->addSuccess($this->__('The product has been saved.'));
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setProductData($data);
                $redirectBack = true;
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            }
        }
        if ($isBackPageCredit) {
            $redirectUrl = $this->getUrl("adminhtml/creditproduct/index");
            $this->_redirectUrl($redirectUrl);
        }else{
            if ($redirectBack) {
                $this->_redirect('*/*/edit', array(
                    'id' => $productId,
                    '_current' => true
                ));
            } elseif ($this->getRequest()->getParam('popup')) {
                $this->_redirect('*/*/created', array(
                    '_current' => true,
                    'id' => $productId,
                    'edit' => $isEdit
                ));
            } else {
                $this->_redirect('*/*/', array('store' => $storeId));
            }
        }


    }

    public function deleteAction()
    {
        $isBackPageCredit=$this->getRequest()->getParam('back_page_credit_product');
        if ($id = $this->getRequest()->getParam('id')) {
            $product = Mage::getModel('catalog/product')
                ->load($id);
            $sku = $product->getSku();
            try {
                $product->delete();
                $this->_getSession()->addSuccess($this->__('The product has been deleted.'));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        if($isBackPageCredit){
            $redirectUrl = $this->getUrl("adminhtml/creditproduct/index");
            $this->_redirectUrl($redirectUrl);
        }else{
            $this->getResponse()
                ->setRedirect($this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store'))));
        }

    }
}