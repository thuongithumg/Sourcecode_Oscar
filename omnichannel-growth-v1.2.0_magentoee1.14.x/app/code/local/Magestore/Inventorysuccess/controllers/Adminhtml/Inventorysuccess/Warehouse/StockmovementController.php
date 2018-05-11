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
 * Inventorysuccess Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_Warehouse_StockmovementController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Get stock on hand grid
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Export warehouse grid to csv file
     */
    public function exportCsvAction()
    {
        $fileName = 'stock_movement.csv';
        $content = $this->getLayout()
            ->createBlock('inventorysuccess/adminhtml_warehouse_edit_tab_stockmovement')
            ->getCsvFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export warehouse grid to xml file
     */
    public function exportXmlAction()
    {
        $fileName = 'stock_movement.xml';
        $content = $this->getLayout()
            ->createBlock('inventorysuccess/adminhtml_warehouse_edit_tab_stockmovement')
            ->getExcelFile();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed(
            'admin/inventorysuccess/stockcontrol/stock_movement_history'
        );
    }
}