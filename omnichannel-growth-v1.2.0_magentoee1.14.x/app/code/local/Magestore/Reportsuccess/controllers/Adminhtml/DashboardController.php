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
 * @package     Magestore_Reportsuccess
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * @category    Magestore
 * @package     Magestore_Reportsuccess
 * @author      Magestore Developer
 */

class Magestore_Reportsuccess_Adminhtml_DashboardController extends Mage_Adminhtml_Controller_Action {

    /**
     * @return $this
     */
    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('reportsuccess/report')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Retailer Report'), Mage::helper('adminhtml')->__('Retailer Report')
            );
        return $this;
    }

    /**
     * @return bool
     */
    public function dashboardAction() {
        if($this->checkPluginIsInstalled() == false){
            return false;
        }
        $this->_initAction();
        $this->renderLayout();
    }

    /**
     *
     */
    public function editColumnsAction(){
        $grid = $this->getRequest()->getParam('grid');
        $code = $this->getRequest()->getParam('columns');
        $removecolumn = Mage::getModel('reportsuccess/editcolumns')->getCollection()->addFieldToFilter('grid',$grid)
            ->getFirstItem();
        if($removecolumn->getId() && $code)
        $removecolumn->setValue($code)->save();
    }

    /**
     *
     */
    public function editMetricsAndDimensionsAction(){
        /* save Metrics */
         $grid = $this->getRequest()->getParam('grid');
         $metrics = $this->getRequest()->getParam('editcolumnsMetrics');
         $removecolumnMetrics = Mage::getModel('reportsuccess/editcolumns')->getCollection()->addFieldToFilter('grid',$grid)
            ->getFirstItem();
         if($removecolumnMetrics->getId() && $metrics)
             $removecolumnMetrics->setValue($metrics)->save();

         /* save Dimensions */
         $dimensions = $this->getRequest()->getParam('editcolumnsDimensions');
         $removecolumnDimensions = Mage::getModel('reportsuccess/editcolumns')->getCollection()->addFieldToFilter('grid',$grid.'dimentions')
            ->getFirstItem();
         if($removecolumnDimensions->getId() && $dimensions)
             $removecolumnDimensions->setValue($dimensions)->save();

    }

    /**
     * @return bool
     */
    public function checkPluginIsInstalled(){
        $helper = Mage::helper('reportsuccess')->inventoryInstalled();
        if(!$helper){
            echo ($this->__('Please install Inventory Management & Purchase Management'));
            return false;
        }
        return true;
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('reportsuccess/report');
    }

}
