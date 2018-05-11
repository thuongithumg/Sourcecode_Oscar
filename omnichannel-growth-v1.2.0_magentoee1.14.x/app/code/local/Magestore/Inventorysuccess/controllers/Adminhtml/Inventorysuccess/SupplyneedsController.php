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
 * Adjuststock Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_SupplyneedsController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_SupplyneedsController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('inventorysuccess/prediction/supply_needs')
            ->_addBreadcrumb(
                $this->__('Supply Needs Management'),
                $this->__('Supply Needs Management')
            )->_title($this->__('Supply Needs Management'));
        return $this;
    }

    /**
     * index action
     */
    public function indexAction()
    {
        $this->_initAction();
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('inventorysuccess/adminhtml_supplyNeeds_edit'))
            ->_addLeft($this->getLayout()->createBlock('inventorysuccess/adminhtml_supplyNeeds_edit_tabs'));
        $this->renderLayout();
    }


    /**
     * product action, list product in supply needs
     */
    public function productAction()
    {
        $this->loadLayout()
            ->renderLayout();
    }

    /**
     * product grid action, use to filter in product list
     */
    public function productGridAction()
    {
        $this->loadLayout()
            ->renderLayout();
    }

    /**
     * show supply needs result
     * @return $this|Mage_Core_Controller_Varien_Action
     */
    public function showsupplyneedsAction()
    {
        $dataPost = $this->getRequest()->getPost();
        $data = array();
        $supplyNeedsService = Magestore_Coresuccess_Model_Service::supplyNeedsService();
        $postField = $supplyNeedsService->getPostFields();
        foreach ($postField as $field) {
            if (isset($dataPost[$field]))
                $data[$field] = $dataPost[$field];
        }
        if (!empty($data)) {
            $topFilter = base64_encode(serialize($data));
            return $this->_redirect('*/*/',
                array(
                    'top_filter' => $topFilter
                )
            );
        }
        return $this->_redirect('*/*/');
    }

    /**
     * export grid item to CSV type
     */
    public function exportListProductsCsvAction()
    {
        $fileName = "SupplyNeeds_" . date('Ymd_His') . ".csv";
        $content = $this->getLayout()
            ->createBlock('inventorysuccess/adminhtml_supplyNeeds_edit_tab_products')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportListProductsXmlAction()
    {
        $fileName = "SupplyNeeds_" . date('Ymd_His') . ".xml";
        $content = $this->getLayout()
            ->createBlock('inventorysuccess/adminhtml_supplyNeeds_edit_tab_products')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('inventorysuccess/prediction/supply_needs');
    }

}