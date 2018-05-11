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
 * Stocktaking Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_Stocktaking_AdjustController extends Mage_Adminhtml_Controller_Action
{
    /**
     * request adjustment
     *
     * @return $this
     */
    public function requestAction()
    {
        $data = $this->getNewAdjustStockData();
        if (count($data)) {
            try{
                $adjustStock = Mage::getModel('inventorysuccess/adjuststock');
                $adjustStockService = Magestore_Coresuccess_Model_Service::adjustStockService();
                $adjustData = $data;
                $adjustData['products'] = array();
                if(count($this->getAdjustProductCollection())) {
                    $adjustData['products'] = $this->getAdjustProductCollection();
                }
                $adjustStockService->createAdjustment($adjustStock, $adjustData);
                /* if created adjuststock then complete it */
                if($adjustStock->getId()) {
                    Mage::getSingleton('admin/session')->addSuccess(
                        Mage::helper('inventorysuccess')->__('A new adjustment has been created.')
                    );
                    return $this->_redirect('*/inventorysuccess_adjuststock/edit', array('id' => $adjustStock->getId()));
                }
                return $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('admin/session')->addError($e->getMessage());
                if($this->getRequest()->getParam('id')) {
                    return $this->_redirect('*/inventorysuccess_adjuststock/edit', array('id' => $this->getRequest()->getParam('id')));
                }
            }
        }
        Mage::getSingleton('admin/session')->addError(
            Mage::helper('inventorysuccess')->__('Unable to find stock adjustment to create.')
        );
        return $this->_redirect('*/*/');
    }

    /**
     * get new adjust stock data
     *
     * @param
     * @return array
     */
    public function getNewAdjustStockData()
    {
        $id = $this->getRequest()->getParam('id');
        $warehouseId = $this->getRequest()->getParam('warehouse_id');
        $stocktakingCode = $this->getRequest()->getParam('stocktaking_code');
        $data = array();
        if(isset($id)){
            $data['warehouse_id'] = $warehouseId;
            $data['reason'] = Mage::helper('inventorysuccess')->__('Adjust stock from stocktaking %1', $stocktakingCode);
        }
        return $data;
    }

    /**
     * get different product collection
     *
     * @param
     * @return array
     */
    public function getAdjustProductCollection()
    {
        $stocktakingId = $this->getRequest()->getParam('id');
        $data = array();
        if(isset($stocktakingId)){
            $collection = Mage::getModel('inventorysuccess/stocktaking_product')->getCollection();
            $stocktakingId = $this->getRequest()->getParam('id');
            $productCollection = $collection->getDifferentProducts($stocktakingId);
            $data = $this->prepareData($productCollection);
        }
        return $data;
    }

    /**
     * prepare stocktaking data
     *
     * @param
     * @return array
     */
    public function prepareData($productCollection)
    {
        $data = array();
        foreach ($productCollection as $productModel) {
            $data[$productModel->getId()]= array(
                'product_sku' => $productModel->getData('product_sku'),
                'adjust_qty' => $productModel->getData('stocktaking_qty')
            );
        }
        return $data;
    }

    /**
     * Determine if authorized to perform group actions.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('inventorysuccess/stockcontrol/create_adjuststock');
    }
}