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
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Storepickup_Adminhtml_Storepickup_ImportController
 */
class Magestore_Storepickup_Adminhtml_Storepickup_ImportController extends Mage_Adminhtml_Controller_Action
{

    /**
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
                ->_setActiveMenu('storepickup/stores')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Stores'), Mage::helper('adminhtml')->__('Import Stores'));

        return $this;
    }

    public function importstoreAction()
    {

        $this->loadLayout();
        $this->_setActiveMenu('storepickup/stores');

        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Stores'), Mage::helper('adminhtml')->__('Import Stores'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Import Stores'), Mage::helper('adminhtml')->__('Import Stores'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $editBlock = $this->getLayout()->createBlock('storepickup/adminhtml_store_edit');
        $editBlock->removeButton('delete');
        $editBlock->removeButton('saveandcontinue');
        $editBlock->removeButton('reset');
        $editBlock->updateButton('back', 'onclick', 'backEdit()');
        $editBlock->setData('form_action_url', $this->getUrl('*/*/save', array()));

        $this->_addContent($editBlock)
                ->_addLeft($this->getLayout()->createBlock('storepickup/adminhtml_store_import_tabs'));

        $this->renderLayout();
    }

    public function saveAction()
    {
        if (!isset($_FILES['csv_store'])) {
            Mage::getSingleton('core/session')->addError('Not selected file!');
            $this->_redirect('*/*/importstore');
            return;
        }

        $oFile = new Varien_File_Csv();
        $data = $oFile->getData($_FILES['csv_store']['tmp_name']);

        $storeData = array();

        try {
            $total = 0;
            $index_row = null;
            $error_message = '';
            $flag = 1;
            foreach ($data as $col => $row) {
                $store = Mage::getModel('storepickup/store');
                if ($col == 0) {
                    $index_row = $row;
                } else {

                    for ($i = 0; $i < count($row); $i++) {
                        $storeData[$index_row[$i]] = $row[$i];
                    }
                    if ($storeData['monday_status'] == 0 || !$storeData['monday_status']) {
                        $storeData['monday_status'] = 1;
                    }

                    if ($storeData['tuesday_status'] == 0 || !$storeData['tuesday_status']) {
                        $storeData['tuesday_status'] = 1;
                    }

                    if ($storeData['wednesday_status'] == 0 || !$storeData['wednesday_status']) {
                        $storeData['wednesday_status'] = 1;
                    }

                    if ($storeData['thursday_status'] == 0 || !$storeData['thursday_status']) {
                        $storeData['thursday_status'] = 1;
                    }

                    if ($storeData['friday_status'] == 0 || !$storeData['friday_status']) {
                        $storeData['friday_status'] = 1;
                    }

                    if ($storeData['saturday_status'] == 0 || !$storeData['saturday_status']) {
                        $storeData['saturday_status'] = 1;
                    }

                    if ($storeData['sunday_status'] == 0 || !$storeData['sunday_status']) {
                        $storeData['sunday_status'] = 1;
                    }

                    if ($storeData['monday_time_interval'] == 0 || !$storeData['monday_time_interval']) {
                        $storeData['monday_time_interval'] = 15;
                    }

                    if ($storeData['tuesday_time_interval'] == 0 || !$storeData['tuesday_time_interval']) {
                        $storeData['tuesday_time_interval'] = 15;
                    }

                    if ($storeData['wednesday_time_interval'] == 0 || !$storeData['wednesday_time_interval']) {
                        $storeData['wednesday_time_interval'] = 15;
                    }

                    if ($storeData['thursday_time_interval'] == 0 || !$storeData['thursday_time_interval']) {
                        $storeData['thursday_time_interval'] = 15;
                    }

                    if ($storeData['friday_time_interval'] == 0 || !$storeData['friday_time_interval']) {
                        $storeData['friday_time_interval'] = 15;
                    }

                    if ($storeData['saturday_time_interval'] == 0 || !$storeData['saturday_time_interval']) {
                        $storeData['saturday_time_interval'] = 15;
                    }

                    if ($storeData['sunday_time_interval'] == 0 || !$storeData['sunday_time_interval']) {
                        $storeData['sunday_time_interval'] = 15;
                    }
                    /* check state - Anthony */
                    $storeData['state_id'] = Mage::helper('storepickup/region')->validateState($storeData['country'], $storeData['state']);

                    if ($storeData['state_id'] == Magestore_Storepickup_Helper_Region::STATE_ERROR) {
                        $_state = $storeData['state'] == '' ? 'null' : $storeData['state'];
                        if ($flag == 1)
                            $error_message .= ' <br />' . $flag . ': ' . $_state . ' of <strong>' . $storeData['store_name'] . '</strong><br />';
                        else
                            $error_message .= $flag . ': ' . $_state . ' of <strong>' . $storeData['store_name'] . '</strong><br />';
                    }
                    /* end check state - Anthony */
                    if ($storeData['store_name'] && $storeData['address'] && $storeData['country'] && $storeData['state_id'] > Magestore_Storepickup_Helper_Region::STATE_ERROR) {
                        $store->setData($storeData);
                        $store->setId(null);
                        if ($store->import()) {
                            $total++;
                        }
                    }
                    $flag ++;
                }
            }

            $this->_redirect('*/storepickup_store/index');
            if ($error_message != '') {
                $error_msg = 'The States that don\'t match any State: ' . $error_message;
                Mage::getSingleton('core/session')->addNotice($error_msg);
            }

            if ($total != 0) {
                Mage::getSingleton('core/session')->addSuccess('Imported successful total ' . $total . ' stores');
            } else {
                Mage::getSingleton('core/session')->addSuccess('No store imported');
            }
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->_redirect('*/*/importstore');
        }
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('storepickup');
    }

}
