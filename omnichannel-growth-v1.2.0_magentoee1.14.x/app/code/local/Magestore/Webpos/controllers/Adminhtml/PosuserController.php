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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Marketingautomation Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_Marketingautomation
 * @author      Magestore Developer
 */
class Magestore_Webpos_Adminhtml_PosuserController extends Mage_Adminhtml_Controller_Action {

    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Marketingautomation_Adminhtml_MarketingautomationController
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('webpos/posuser')
                ->_addBreadcrumb(
                        Mage::helper('adminhtml')->__('POS User Manager'), Mage::helper('adminhtml')->__('POS User Manager')
        );
        return $this;
    }

    /**
     * index action
     */
    public function indexAction() {
        $this->_initAction()
                ->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction() {
        $userId = $this->getRequest()->getParam('id');
        $modelCollection = Mage::getModel('webpos/user')->getCollection()
                ->addFilterToMap('user_id', 'main_table.user_id')
                ->addFieldToFilter('user_id', array('eq' => $userId));
        $model = $modelCollection->getFirstItem();
        if ($model->getId() || $userId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getUserData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('user_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('webpos/posuser');

            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('POS User Manager'), Mage::helper('adminhtml')->__('POS User Manager')
            );
            $this->_addBreadcrumb(
                    Mage::helper('adminhtml')->__('POS User News'), Mage::helper('adminhtml')->__('POS User News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('webpos/adminhtml_user_edit'))
                    ->_addLeft($this->getLayout()->createBlock('webpos/adminhtml_user_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('webpos')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    /**
     * save item action
     */
    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('webpos/user');
            if (isset($data['location_id']) && $data['location_id']) {
                $data['location_id'] = implode(',', $data['location_id']);
            }
            if (isset($data['pos_ids']) && $data['pos_ids']) {
                $data['pos_ids'] = implode(',', $data['pos_ids']);
            }
            $model->setData($data)
                    ->setId($this->getRequest()->getParam('id'));

            /* Edit by Dante - Trinh Huy Hoang - 28/08/2015 */
            if (isset($data['location_id']) && is_null($data['location_id'])) {
                $model->setLocationId(1);
            }
            if (isset($data['warehouse_id'])) {
                $model->setLocationId($data['warehouse_id']);
            }
            /* End edit by Dante */
            /*
             * Unsetting new password and password confirmation if they are blank
             */
            if ($model->hasNewPassword() && $model->getNewPassword() === '') {
                $model->unsNewPassword();
            }
            if ($model->hasPasswordConfirmation() && $model->getPasswordConfirmation() === '') {
                $model->unsPasswordConfirmation();
            }
            $result = $model->validate(); /* validate data */
            if (is_array($result)) {
                Mage::getSingleton('adminhtml/session')->setUserData($data);
                foreach ($result as $message) {
                    Mage::getSingleton('adminhtml/session')->addError($message);
                }
                $this->_redirect('*/*/edit', array('_current' => true));
                return $this;
            }

            try {
                $storeIds = $model->getData('store_ids');
                if (isset($storeIds) && is_array($storeIds) && count($storeIds)) {
                    $model->setData('store_ids', implode(',', $storeIds));
                }
                $customerGroups = $model->getData('customer_group');
                if (isset($customerGroups) && is_array($customerGroups) && count($customerGroups)) {
                    $model->setData('customer_group', implode(',', $customerGroups));
                }
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('webpos')->__('User was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setUserData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setUserData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('webpos')->__('Unable to find user to save')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete item action
     */
    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('webpos/user');
                $model->setId($this->getRequest()->getParam('id'))
                        ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Item was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * mass delete item(s) action
     */
    public function massDeleteAction() {
        $userIds = $this->getRequest()->getParam('user');
        if (!is_array($userIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($userIds as $userId) {
                    $user = Mage::getModel('webpos/user')->load($userId);
                    $user->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($userIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass change status for item(s) action
     */
    public function massStatusAction() {
        $userIds = $this->getRequest()->getParam('user');
        if (!is_array($userIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($userIds as $userId) {
                    Mage::getSingleton('webpos/user')
                            ->load($userId)
                            ->setStatus($this->getRequest()->getParam('status'))
                            ->setIsMassupdate(true)
                            ->save();
                }
                $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were successfully updated', count($userIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction() {
        $fileName = 'pos_user.csv';
        $content = $this->getLayout()
                ->createBlock('webpos/adminhtml_user_grid')
                ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction() {
        $fileName = 'pos_user.xml';
        $content = $this->getLayout()
                ->createBlock('webpos/adminhtml_user_grid')
                ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed() {
        return Mage::getSingleton('admin/session')->isAllowed('sales/webpos/manage_webpos_permission/manage_webpos_user');
    }

}
