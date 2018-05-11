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
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_WarehouseController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init layout, menu and breadcrumb
     *
     * @return $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('inventorysuccess/stocklisting/warehouse_list')
            ->_addBreadcrumb(
                $this->__('Inventory'),
                $this->__('Inventory')
            )
            ->_title($this->__('Inventory'))
            ->_title($this->__('Manage Warehouse'));
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
     * index action
     */
    public function gridAction()
    {
        $this->loadLayout()
            ->renderLayout();
    }

    /**
     * Initialize warehouse model instance
     *
     * @param $warehouseId |null
     * @return Magestore_Inventorysuccess_Model_Warehouse|false
     */
    protected function _initWarehouse($warehouseId = null)
    {
        $warehouse = Mage::getModel('inventorysuccess/warehouse');
        try {
            $warehouse->load($warehouseId);
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('This warehouse no longer exists.'));
            return $this->_redirect('*/*/');
        }
        Mage::register('inventorysuccess_warehouse', $warehouse);
        Mage::register('current_warehouse', $warehouse);
        return $warehouse;
    }

    /**
     * Customer orders grid
     *
     */
    public function ordersAction() {
        $warehouseId = $this->getRequest()->getParam('id');
        $this->_initWarehouse($warehouseId);
        $this->loadLayout();
        $this->renderLayout();
    }

    protected function renderWarehouse()
    {
        $warehouseId = $this->getRequest()->getParam('id');
        $warehouse = $this->_initWarehouse($warehouseId);

        if ($warehouseId && (!$warehouse || $warehouse->getWarehouseId() != $warehouseId)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('This warehouse no longer exists.'));
            return $this->_redirect('*/*/');
        }

        $this->_initAction()
            ->_addBreadcrumb($this->__('Manage Warehouse'), $this->__('Manage Warehouse'))
            ->_addBreadcrumb($this->__('Warehouse'), $this->__('Warehouse'))
            ->_title('Warehouse')
            ->renderLayout();
    }

    /**
     * Create new warehouse action
     */
    public function newAction()
    {
        return $this->renderWarehouse();
    }

    /**
     * View and edit warehouse action
     *
     * @return $this|Mage_Core_Controller_Varien_Action
     */
    public function editAction()
    {
        return $this->renderWarehouse();
    }

    public function saveAction()
    {
        $params = $this->getRequest()->getParams();
        $id = isset($params['warehouse_id']) && $params['warehouse_id'] > 0 ? $params['warehouse_id'] : null;
        if ($id && !Magestore_Coresuccess_Model_Service::permissionService()->checkPermission(
                'admin/inventorysuccess/stocklisting/warehouse_list/view_warehouse/edit_general_information',
                Mage::getModel('inventorysuccess/warehouse')->load($id)
            )
        ) {
            Mage::getSingleton('adminhtml/session')->addError('You can not edit general information for this warehouse');
            return $this->_redirect('*/*/edit', array('id' => $id));
        }
        $warehouse = Mage::getModel('inventorysuccess/warehouse');
        $warehouse->setData($params);

        if ($warehouse->checkWarehouseCode($id) > 0) {
            $message = Mage::helper('inventorysuccess')->__('The warehouse code (%s) is existed.', $warehouse->getWarehouseCode());
            Mage::getSingleton('adminhtml/session')->addError($message);
            $warehouse = $warehouse->load($id);
            $params['warehouse_code'] = $warehouse->getWarehouseCode();
            Mage::getSingleton('adminhtml/session')->setData('warehouse_param', $params);
            if ($id)
                return $this->_redirect('*/*/edit', array('id' => $id));
            return $this->_redirect('*/*/new');
        }

        try {
            $warehouse->setId($id)->save();
            if (!$id) {
                $this->setWarehousePermission($warehouse);
            }
            $this->mappingLocation($warehouse);

            /* set storeIds to warehouse */
            $storeIds = isset($params['stores']) ? $params['stores'] : array();
            Magestore_Coresuccess_Model_Service::warehouseStoreService()
                    ->setStoresToWarehouse($storeIds, $warehouse->getId());

            Mage::dispatchEvent(
                'controller_after_save_warehouse', array('warehouse' => $warehouse, 'request_params' => $params)
            );
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('inventorysuccess')
                ->__('The warehouse has been saved.'));
        } catch (\Exception $ex) {
            Mage::getSingleton('adminhtml/session')->addError($ex->getMessage());
            Mage::getSingleton('adminhtml/session')->setData('warehouse_param', $params);
            if ($id)
                return $this->_redirect('*/*/edit', array('id' => $warehouse->getWarehouseId()));
            return $this->_redirect('*/*/new');
        }
        if (!empty($params['back']))
            return $this->_redirect('*/*/edit', array('id' => $warehouse->getWarehouseId()));
        return $this->_redirect('*/*/index');
    }

    /**
     * @param Magestore_Inventorysuccess_Model_Warehouse $warehouse
     */
    public function setWarehousePermission($warehouse)
    {
        /** @var $user Mage_Admin_Model_User */
        $user = Mage::getSingleton('admin/session')->getUser();
        $roleId = $user->getRole()->getId();
        if ($roleId) {

            /** @var Magestore_Inventorysuccess_Model_Permission $permission */
            $permission = Mage::getModel('inventorysuccess/permission');
            $permission->setUserId($user->getId());
            $permission->setObjectType($warehouse->getPermissionType());
            $permission->setObjectId($warehouse->getWarehouseId());
            $permission->setRoleId($roleId);
            $permission->save();
        }
    }

    /**
     * Mapping Location
     */
    public function mappingLocation($warehouse)
    {
        $force = true;
        if (Mage::helper('core')->isModuleEnabled('Magestore_Webpos')) {
            try {
                Mage::getModel('webpos/userlocation');
            } catch (\Exception $e) {
                return $this;
            }
        } else {
            return $this;
        }
        $warehouseId = $warehouse->getId();
        $locationId = $warehouse->getLocationId();
        if (!$locationId || !$warehouseId) {
            return $this;
        }
        $locationService = Magestore_Coresuccess_Model_Service::locationService();
        $locationService->mappingWarehouseToLocation($warehouseId, $locationId, $force);
    }

    /**
     * Get stock on hand tab
     */
    public function stockonhandAction()
    {
        $warehouseId = $this->getRequest()->getParam('id');
        $this->_initWarehouse($warehouseId);
        $this->loadLayout()
            ->renderLayout();
    }

    /**
     * Get warehouse permission tab
     */
    public function permissionAction()
    {
        $this->loadLayout()
            ->renderLayout();
    }

    /**
     * Get stock movement tab
     */
    public function stockmovementAction()
    {
        $this->loadLayout()
            ->renderLayout();
    }

    /**
     * Get dashboard tab
     */
    public function dashboardAction()
    {
        $this->loadLayout()
            ->renderLayout();
    }

    public function deleteAction()
    {
        $warehouseId = $this->getRequest()->getParam('id');
        $warehouse = Mage::getResourceModel('inventorysuccess/warehouse_product_collection')
            ->getTotalQtysFromWarehouse($warehouseId);
        if ($warehouse->getSumTotalQty() > 0 || $warehouse->getSumQtyToShip() > 0) {
            Mage::getSingleton('adminhtml/session')->addError(
                $this->__('Can not delete this warehouse because it still contains some products')
            );
            return $this->_redirect('*/*/edit', array('id' => $warehouseId));
        }
        try {
            $warehouse = Mage::getModel('inventorysuccess/warehouse')->load($warehouseId);
            if($warehouse->getIsPrimary()){
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Can not delete a primary warehouse.'));
                return $this->_redirect('*/*/edit', array('id' => $warehouseId));
            }
            $warehouse->delete();
            Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Warehouse has been successfully deleted.'));
        } catch (\Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            return $this->_redirect('*/*/edit', array('id' => $warehouseId));
        }
        return $this->_redirect('*/*/');
    }

    /**
     * Export warehouse grid to csv file
     */
    public function exportCsvAction()
    {
        $fileName = 'warehouse.csv';
        $content = $this->getLayout()->createBlock('inventorysuccess/adminhtml_warehouse_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export warehouse grid to xml file
     */
    public function exportXmlAction()
    {
        $fileName = 'warehouse.xml';
        $content = $this->getLayout()->createBlock('inventorysuccess/adminhtml_warehouse_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    /**
     * Export stock onhand csv file
     */    
    public function exportStockOnHandCsvAction()
    {
        $warehouseId = $this->getRequest()->getParam('warehouse_id', false);
        $warehouseinfo = '';
        if($warehouseId) {
            $warehouse = Mage::getModel('inventorysuccess/warehouse')->load($warehouseId);
            $warehouseinfo = $warehouse->getWarehouseCode() . '-';
        }
        $fileName = 'warehouse-stock-onhand-'. $warehouseinfo . date('Ymd') .'.csv';
        //$content  = $this->getLayout()->createBlock('inventorysuccess/adminhtml_manageStock_product_grid')->getCsvFile();
        $content  = $this->getLayout()->createBlock('inventorysuccess/adminhtml_warehouse_edit_tab_stockonhand_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);        
    }
    
    /**
     * Export stock onhand xml file
     */        
    public function exportStockOnHandXmlAction()
    {
        $warehouseId = $this->getRequest()->getParam('warehouse_id', false);
        $warehouseinfo = '';
        if($warehouseId) {
            $warehouse = Mage::getModel('inventorysuccess/warehouse')->load($warehouseId);
            $warehouseinfo = $warehouse->getWarehouseCode() . '-';
        }        
        $fileName = 'warehouse-stock-onhand-'. $warehouseinfo . date('Ymd') .'.xml';
        //$content  = $this->getLayout()->createBlock('inventorysuccess/adminhtml_manageStock_product_grid')->getXml();
        $content  = $this->getLayout()->createBlock('inventorysuccess/adminhtml_warehouse_edit_tab_stockonhand_grid')->getXml();
        $content = str_replace('sum_', '', $content);
        $this->_prepareDownloadResponse($fileName, $content);         
    }      

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        $resource = 'admin/inventorysuccess/stocklisting/warehouse_list';
        switch ($this->getRequest()->getActionName()) {
            case 'new' :
                $resource .= '/create_warehouse';
                break;
            case 'edit':
                $resource .= '/view_warehouse';
                break;
            case 'save':
                if ($this->getRequest()->getParam('id'))
                    $resource .= '/view_warehouse/edit_general_information';
                else
                    $resource .= '/create_warehouse';
                break;
            case 'delete':
                $resource .= '/view_warehouse/delete_warehouse';
                break;
            case 'stockonhand':
                $resource .= '/view_warehouse/view_stock_on_hand';
                break;
            case 'stockmovement':
                $resource = 'admin/inventorysuccess/stockcontrol/stock_movement_history';
                break;
            case 'permission':
                $resource .= '/view_warehouse/manage_permission';
                break;
        }
        return Mage::getSingleton('admin/session')->isAllowed($resource);
    }
}