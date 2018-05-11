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
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_Warehouse_Permission_StaffController
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
    
    public function saveAction(){
        $warehouseId = $this->getRequest()->getParam('warehouse_id', false);
        $selectedItems = json_decode($this->getRequest()->getParam('selected_items'), true);
        $data = array();
        if($warehouseId && count($selectedItems)>0){
            foreach ($selectedItems as $userId => $value){
                $data[] = array(
                    Magestore_Inventorysuccess_Model_Permission::USER_ID    => $userId,
                    Magestore_Inventorysuccess_Model_Permission::ROLE_ID    => $value['role_id']
                );
            }
            try{
                Magestore_Coresuccess_Model_Service::permissionService()->setPermissionsByObject(
                    Mage::getModel('inventorysuccess/warehouse')->load($warehouseId),
                    null,
                    $data
                );
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Staffs have been added successfully.')
                );
            }catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError(
                    $this->__('Could not add the staffs')
                );
            }
        }
        return $this->_forward('grid');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')
            ->isAllowed('admin/inventorysuccess/stocklisting/warehouse_list/view_warehouse/manage_permission');
    }
}