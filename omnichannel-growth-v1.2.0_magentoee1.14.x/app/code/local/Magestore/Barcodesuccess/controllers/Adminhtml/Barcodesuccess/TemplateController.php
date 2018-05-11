<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 *   @category    Magestore
 *   @package     Magestore_Barcodesuccess
 *   @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Barcodesuccess Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @author      Magestore Developer
 */
class Magestore_Barcodesuccess_Adminhtml_Barcodesuccess_TemplateController extends
    Mage_Adminhtml_Controller_Action
{

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/barcodesuccess/template');
    }

    /**
     * index action
     */
    public function indexAction()
    {
        $this->loadLayout()
             ->_setActiveMenu('barcodesuccess/template')
             ->_addBreadcrumb(
                 Mage::helper('adminhtml')->__('Manage Barcode Label Templates'),
                 Mage::helper('adminhtml')->__('Manage Barcode Label Templates')
             )->_title(Mage::helper('barcodesuccess')->__('Barcode Label Templates'));
        return $this->renderLayout();
    }

    /**
     * @return Mage_Core_Controller_Varien_Action
     */
    public function gridAction()
    {
        return $this->loadLayout()->renderLayout();
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName = 'barcode_templates.csv';
        $content  = $this->getLayout()
                         ->createBlock('barcodesuccess/adminhtml_template_grid')
                         ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * mass delete templates
     */
    public function massDeleteAction()
    {
        $templateIds = $this->getRequest()->getParam('template_id');
        if ( !is_array($templateIds) || empty($templateIds) ) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                $success = 0;
                foreach ( $templateIds as $templateId ) {
                    $template = Mage::getModel('barcodesuccess/template')->load($templateId);
                    $template->delete();
                    $success++;
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Total of %d record(s) has been successfully deleted.',
                              $success)
                );
            } catch ( Exception $e ) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * mass update status
     */
    public function massStatusAction()
    {
        $templateIds = $this->getRequest()->getParam('template_id');
        if ( !is_array($templateIds) || empty($templateIds) ) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                $success = 0;
                $status  = $this->getRequest()->getParam('status');
                foreach ( $templateIds as $templateId ) {
                    $template = Mage::getModel('barcodesuccess/template')
                                    ->load($templateId);
                    $template->setStatus($status)
                             ->setIsMassupdate(true)
                             ->setId($template->getId())
                             ->save();
                    $success++;
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) has been successfully updated.', $success)
                );
            } catch ( Exception $e ) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName = 'barcode_templates.xml';
        /*$content  = $this->getLayout()
                         ->createBlock('barcodesuccess/adminhtml_template_grid')
                         ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);*/
        $grid       = $this->getLayout()->createBlock('barcodesuccess/adminhtml_template_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $templateId = $this->getRequest()->getParam('id');
        $template   = Mage::getModel('barcodesuccess/template')->load($templateId);

        if ( $template->getId() || $templateId == null ) {

            /* Add by Peter - barcode attribute - 03/02/2017 */
            if($template->getProductAttributeShowOnBarcode()){
                $template['product_attribute_show_on_barcode'] = explode(',',$template->getProductAttributeShowOnBarcode());
            }
            /*End by Peter */

            Mage::register('template_data', $template);
            $this->loadLayout();
            $this->_setActiveMenu('barcodesuccess/barcodesuccess');
            $this->_addBreadcrumb(
                Mage::helper('barcodesuccess')->__('Template Information'),
                Mage::helper('barcodesuccess')->__('Template Information')
            );
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('barcodesuccess/adminhtml_template_edit'))
                 ->_addLeft($this->getLayout()->createBlock('barcodesuccess/adminhtml_template_edit_tabs'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('barcodesuccess')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    public function saveAction()
    {
        /* Add by Peter - barcode attribute - 03/02/2017 */
//        $update_table_template = Mage::getStoreConfig('barcodesuccess/template/update_v1');
//        if(!$update_table_template){
//            Mage::getConfig()->saveConfig('barcodesuccess/template/update_v1',900);
//            $coreResource = Mage::getSingleton('core/resource');
//            $coreResource->getConnection('core_write')->query("
//                ALTER TABLE  {$coreResource->getTableName('os_barcode_template')} ADD rotate VARCHAR(255);
//                ALTER TABLE  {$coreResource->getTableName('os_barcode_template')} ADD product_attribute_show_on_barcode VARCHAR(255) AFTER bottom_margin;
//             ");
//        }
        /*End by Peter */

        if ( $data = $this->getRequest()->getPost() ) {
            /* Edit by Peter - barcode attribute - 03/02/2017 */
            if(isset($data['product_attribute_show_on_barcode'])){
                $data['product_attribute_show_on_barcode'] =  implode(',',$data['product_attribute_show_on_barcode']);
            }else{
                $data['product_attribute_show_on_barcode'] = '';
            }
            /*End by Peter */
            $model = Mage::getModel('barcodesuccess/template');
            $model->setData($data)
                  ->setId($this->getRequest()->getParam('id'));
            try {
                $model->save();
                $this->_getSession()->addSuccess(Mage::helper('barcodesuccess')->__('Barcode Template has been successfully saved.'));
                $this->_getSession()->setFormData(false);
                if ( $this->getRequest()->getParam('back') ) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch ( Exception $e ) {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_getSession()->addError(Mage::helper('barcodesuccess')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ( $id = $this->getRequest()->getParam('id') ) {
            $model = Mage::getModel('barcodesuccess/template');
            $model->load($id);
            try {
                $model->delete();
                $this->_getSession()->addSuccess(Mage::helper('barcodesuccess')->__('Barcode Template has been successfully deleted.'));
            } catch ( Exception $e ) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
        return;
    }

    public function loadDefaultAction()
    {
        $this->_initJSONResponse();
        $barcodeType = $this->getRequest()->getParam('barcodeType');
        $defaultData = Mage::getSingleton('barcodesuccess/source_template_type')->getDefaultData($barcodeType);
        $this->getResponse()->appendBody(json_encode($defaultData));
    }

    public function loadPreviewAction()
    {
        $this->_initJSONResponse();
        $row               = $this->getRequest()->getParam('label_per_row') ? $this->getRequest()->getParam('label_per_row') : 1;
        $barcodeCollection = Mage::getModel('barcodesuccess/barcode')->getCollection()->setCurPage(1)->setPageSize($row);
        $barcodeData       = array();
        foreach ( $barcodeCollection as $barcode ) {
            $barcodeData[] = $barcode->getData();
        }
        $content = Mage::getBlockSingleton('barcodesuccess/barcode_template')
                       ->setTemplateData($this->getRequest()->getParams())
                       ->setBarcodes($barcodeData)
                       ->toHtml();
        $this->getResponse()->appendBody(json_encode(array('content' => $content)));
    }

    /**
     * @return Zend_Controller_Response_Abstract
     */
    protected function _initJSONResponse()
    {
        return $this->getResponse()->setHeader('Content-Type', 'application/json');
    }

}
