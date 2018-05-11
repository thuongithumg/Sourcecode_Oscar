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
 * @package     Magestore_Suppliersuccess
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Suppliersuccess Adminhtml Controller
 * 
 * @category    Magestore
 * @package     Magestore_Suppliersuccess
 * @author      Magestore Developer
 */
class Magestore_Suppliersuccess_Adminhtml_Suppliersuccess_SupplierController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Magestore_Suppliersuccess_Adminhtml_SuppliersuccessController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('suppliersuccess/supplier')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Supplier Management'),
                Mage::helper('adminhtml')->__('Supplier Management')
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
        $supplierId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('suppliersuccess/supplier')->load($supplierId);

        if ($model->getId() || $supplierId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('supplier_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('suppliersuccess/supplier');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Supplier Management'),
                Mage::helper('adminhtml')->__('Supplier Management')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('suppliersuccess/adminhtml_supplier_edit'))
                ->_addLeft($this->getLayout()->createBlock('suppliersuccess/adminhtml_supplier_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('suppliersuccess')->__('Supplier does not exist')
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
            $model = Mage::getModel('suppliersuccess/supplier');        
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));
            
            try {
                if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }
                $model->save();

                if(isset($data['products'])) {
                    $productData = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['products']);
                    Magestore_Coresuccess_Model_Service::supplierService()->setProductsToSupplier($model, $productData);
                }
//
//                if(isset($data['pricelists'])) {
//                    $pricelist = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['pricelists']);
//                    if($pricelist) {
//                        Magestore_Coresuccess_Model_Service::supplierPricelistService()->massUpdate($pricelist);
//                    }
//                }

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('suppliersuccess')->__('Supplier was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('suppliersuccess')->__('There was an error when saving supplier information.'));
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('suppliersuccess')->__('(Please note that the Supplier code must be unique)'));
                Mage::getSingleton('adminhtml/session')->setFormData($model->getData());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('suppliersuccess')->__('Unable to find supplier to save')
        );
        $this->_redirect('*/*/');
    }
 
    /**
     * delete item action
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('suppliersuccess/supplier');
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Supplier was successfully deleted')
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
    public function massDeleteAction()
    {
        $supplierIds = $this->getRequest()->getParam('supplier');
        if (!is_array($supplierIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select supplier(s)'));
        } else {
            try {
                foreach ($supplierIds as $supplierId) {
                    $supplier = Mage::getModel('suppliersuccess/supplier')->load($supplierId);
                    $supplier->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted',
                    count($supplierIds))
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
        $supplierIds = $this->getRequest()->getParam('supplier');
        if (!is_array($supplierIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($supplierIds as $supplierId) {
                    Mage::getSingleton('suppliersuccess/supplier')
                        ->load($supplierId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($supplierIds))
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
        $fileName   = 'suppliers.csv';
        $content    = $this->getLayout()
                           ->createBlock('suppliersuccess/adminhtml_supplier_grid')
                           ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName   = 'suppliers.xml';
        $content    = $this->getLayout()
                           ->createBlock('suppliersuccess/adminhtml_supplier_grid')
                           ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('suppliersuccess/supplier');
    }
}