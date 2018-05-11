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
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_Lowstocknotification_NotificationController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Inventorysuccess_Adminhtml_InventorysuccessController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('inventorysuccess/prediction/lowstock_notification')
            ->_addBreadcrumb(
                $this->__('Manage Notification Log'),
                $this->__('Manage Notification Log')
            )
            ->_title($this->__('Manage Notification Log'));
        return $this;
    }
 
    /**
     * index action
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * notify link will be redirect to view notification detail
     * @return $this
     */
    public function notifyAction()
    {
        return $this;
    }

    /**
     * view and edit item action
     */
    public function editAction()
    {
        $notificationId = $this->getRequest()->getParam('id');
        /** @var Magestore_Inventorysuccess_Model_LowStockNotification_Notification $model */
        $model  = Mage::getModel('inventorysuccess/lowStockNotification_notification')->load($notificationId);
        if ($model->getId() || $notificationId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('lowstocknotification_notification_data', $model);

            $this->_initAction()->_title($this->__('View Notification Log'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('inventorysuccess/adminhtml_lowStockNotification_notification_edit'))
                ->_addLeft($this->getLayout()->createBlock('inventorysuccess/adminhtml_lowStockNotification_notification_edit_tabs'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventorysuccess')->__('Notification log does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    /**
     * product action, list product in low stock notification
     */
    public function productAction() {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * product grid action, use to filter in product list
     */
    public function productGridAction() {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * export grid item to CSV type
     */
    public function exportListProductsCsvAction()
    {
        $fileName = "Lowstock_listing_". date('Ymd_His').".csv";
        $content    = $this->getLayout()
            ->createBlock('inventorysuccess/adminhtml_lowStockNotification_notification_edit_tab_products')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportListProductsXmlAction()
    {
        $fileName = "Lowstock_listing_". date('Ymd_His').".xml";
        $content    = $this->getLayout()
            ->createBlock('inventorysuccess/adminhtml_lowStockNotification_notification_edit_tab_products')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('inventorysuccess/prediction/lowstock_notification');
    }

}