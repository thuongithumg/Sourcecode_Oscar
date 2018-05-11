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
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_Warehouse_LocationController extends Mage_Adminhtml_Controller_Action
{
    /**
     * view and edit item action
     */
    public function mappingAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('inventorysuccess/stocklisting');

        $this->_addBreadcrumb(
            Mage::helper('adminhtml')->__('Mapping'),
            Mage::helper('adminhtml')->__('Mapping')
        );
        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('inventorysuccess/adminhtml_location_edit'))
            ->_addLeft($this->getLayout()->createBlock('inventorysuccess/adminhtml_location_edit_tabs'));
        $this->renderLayout();
    }

    /**
     * warehouse action
     */
    public function warehouseAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('inventorysuccess.location.edit.tab.warehouse')
             ->setWarehouse($this->getRequest()->getPost('location_warehouse', null));
        $this->renderLayout();
    }

    /**
     * warehouse action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            if(isset($data['warehouse'])) {
                $data['warehouse'] = Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['warehouse']);
                $locationService = Magestore_Coresuccess_Model_Service::locationService();
                try {
                    $locationService->createListMapping($data['warehouse']);
                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('inventorysuccess')
                                                                        ->__('The mapping has been saved.'));
                }catch (Exception $e){
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }else{
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('inventorysuccess')
                                                            ->__('Please select a location'));
            }

        }
        return $this->_redirect('*/*/mapping');
    }

    /**
     * product grid action
     */
    public function locationGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        $resource = 'admin/inventorysuccess/stocklisting/warehouse_location_mapping';
        return Mage::getSingleton('admin/session')->isAllowed($resource);
    }    
}