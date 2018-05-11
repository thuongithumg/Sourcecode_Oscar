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
 * @package     Magestore_Giftvoucher
 * @module     Giftvoucher
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Adminhtml Giftproduct controller
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Magestore_Giftvoucher_Adminhtml_Giftvoucher_GiftproductController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Initialize action
     *
     * @return Magestore_Giftvoucher_Adminhtml_GiftproductController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('giftvoucher/giftproduct')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Gift Product Manager'), 
                Mage::helper('adminhtml')->__('Gift Product Manager'));

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Gift Prodduct'))
            ->_title($this->__('Manage Gift Product'));
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Create new Gift product action
     */
    public function newAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('giftvoucher/giftvoucher');
        $session = Mage::getSingleton('giftvoucher/session');
        $session->setData('gift_product_edit', 1);
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock('giftvoucher/adminhtml_giftproduct_edit'))
            ->_addLeft($this->getLayout()->createBlock('giftvoucher/adminhtml_giftproduct_edit_tabs'));

        $this->renderLayout();
    }

    /**
     * Edit Gift product action
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $session = Mage::getSingleton('giftvoucher/session');
        $session->setData('gift_product_edit', 1);
        $this->_redirect("adminhtml/catalog_product/edit", array('id' => $id));
    }

    /**
     * Update attribute action
     */
    public function updateAttributeAction()
    {
        $this->_redirect("adminhtml/catalog_product_action_attribute/edit");
    }

    /**
     * Delete Gift product in mass number
     */
    public function massDeleteAction()
    {
        $productIds = $this->getRequest()->getParam('product');
        if (!is_array($productIds)) {
            $this->_getSession()->addError($this->__('Please select product(s).'));
        } else {
            if (!empty($productIds)) {
                try {
                    foreach ($productIds as $productId) {
                        $product = Mage::getSingleton('catalog/product')->load($productId);
                        Mage::dispatchEvent('catalog_controller_product_delete', array('product' => $product));
                        $product->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($productIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Update product(s) status action
     *
     */
    public function massStatusAction()
    {
        $productIds = (array) $this->getRequest()->getParam('product');
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        $status = (int) $this->getRequest()->getParam('status');

        try {
            $this->_validateMassStatus($productIds, $status);
            Mage::getSingleton('catalog/product_action')
                ->updateAttributes($productIds, array('status' => $status), $storeId);

            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) have been updated.', count($productIds))
            );
        } catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()
                ->addException($e, $this->__('An error occurred while updating the product(s) status.'));
        }

        $this->_redirect('*/*/', array('store' => $storeId));
    }

    /**
     * Validate batch of products before theirs status will be set
     *
     * @throws Mage_Core_Exception
     * @param  array $productIds
     * @param  int $status
     * @return void
     */
    public function _validateMassStatus(array $productIds, $status)
    {
        if ($status == Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
            if (!Mage::getModel('catalog/product')->isProductsHasSku($productIds)) {
                throw new Mage_Core_Exception(
                $this->__('Some of the processed products have no SKU value defined. Please fill it prior to performing operations on these products.')
                );
            }
        }
    }

    /**
     * Grid action
     */
    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('giftvoucher/adminhtml_giftproduct_grid')->toHtml()
        );
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('giftvoucher/giftproduct');
    }

}
