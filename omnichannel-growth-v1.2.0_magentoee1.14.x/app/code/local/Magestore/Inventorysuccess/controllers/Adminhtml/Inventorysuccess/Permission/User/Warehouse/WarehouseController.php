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
 * Permission User Edit Tab Warehouse Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_Permission_User_Warehouse_WarehouseController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Reload User Edit Tab Warehouse Grid
     *
     * @return Mage_Core_Controller_Varien_Action
     */
    public function gridAction(){
        return $this->loadLayout()
            ->renderLayout();
    }

    public function saveAction(){
        $staffId = $this->getRequest()->getParam('user_id');
        $selectedItems = json_decode($this->getRequest()->getParam('selected_items'), true);
        $data = array();
        foreach ($selectedItems as $warehouseId => $value){
            if(!isset($value['role_id'])){continue;}
            $data[] = array(
                Magestore_Inventorysuccess_Model_Permission::USER_ID    => $staffId,
                Magestore_Inventorysuccess_Model_Permission::OBJECT_ID    => $warehouseId,
                Magestore_Inventorysuccess_Model_Permission::ROLE_ID    => $value['role_id']
            );
        }
        try{
            Magestore_Coresuccess_Model_Service::permissionService()->setPermissionsByObject(
                Mage::getModel('inventorysuccess/warehouse'),
                null,
                $data
            );
            Mage::getSingleton('adminhtml/session')->addSuccess(
                $this->__('Warehouse permission has been added successfully.')
            );
        }catch (Exception $e){
            Mage::getSingleton('adminhtml/session')->addError(
                $this->__('Could not add the warehouse permission')
            );
        }
    
        return $this->_forward('grid');
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')
            ->isAllowed('admin/inventorysuccess/stocklisting/warehouse_list/view_warehouse/manage_permission');
    }
}