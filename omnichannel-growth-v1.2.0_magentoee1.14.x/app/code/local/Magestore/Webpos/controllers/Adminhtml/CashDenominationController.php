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

/*
 * Web POS by Magestore.com
 * Version 2.3
 * Updated by Daniel - 12/2015
 */

class Magestore_Webpos_Adminhtml_CashDenominationController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('webpos/manage_webpos_denomination')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('POS Denomination Manager'),
                Mage::helper('adminhtml')->__('POS Denomination Manager')
            );
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
     * view and edit item action
     */
    public function editAction()
    {
        $denominationId     = $this->getRequest()->getParam('id');
        $model = Mage::getModel('webpos/denomination')->load($denominationId);
        if ($model->getId() || $denominationId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('denomination_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('webpos/manage_webpos_denomination');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('POS Denomination Manager'),
                Mage::helper('adminhtml')->__('POS Denomination Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('POS Denomination News'),
                Mage::helper('adminhtml')->__('POS Denomination News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('webpos/adminhtml_cashdenomination_edit'))
                ->_addLeft($this->getLayout()->createBlock('webpos/adminhtml_cashdenomination_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('webpos')->__('Denomination does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * save item action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('webpos/denomination');
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('webpos')->__('Denomination was successfully saved')
                );
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
            Mage::helper('webpos')->__('Unable to find denomination to save')
        );
        $this->_redirect('*/*/');
    }

    /**
     * delete item action
     */
    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('webpos/denomination');
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Denomination was successfully deleted')
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
        $denominationIds = $this->getRequest()->getParam('denomination');
        if (!is_array($denominationIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select denomination(s)'));
        } else {
            try {
                foreach ($denominationIds as $denominationId) {
                    $denomination = Mage::getModel('webpos/denomination')->load($denominationId);
                    $denomination->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted', count($denominationIds))
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

    public function massStatusAction()
    {
        $denominationIds = $this->getRequest()->getParam('denomination');
        if (!is_array($denominationIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select denomination(s)'));
        } else {
            try {
                foreach ($denominationIds as $denominationId) {
                    Mage::getSingleton('webpos/denomination')
                        ->load($denominationId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($denominationIds))
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
    public function exportCsvAction()
    {
        $fileName   = 'pos_denomination.csv';
        $content    = $this->getLayout()
            ->createBlock('webpos/adminhtml_cashdenomination_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName   = 'pos_denomination.xml';
        $content    = $this->getLayout()
            ->createBlock('webpos/adminhtml_cashdenomination_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/webpos/manage_webpos_denomination');
    }
}