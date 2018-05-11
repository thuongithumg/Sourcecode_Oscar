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
 * Inventorysuccess Model
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Service_OrderProcess_OrderProcessService
    extends Magestore_Inventorysuccess_Model_Service_OrderProcess_AbstractService
{
    const CHANGE_WAREHOUSE_PERMISSION = 'inventorysuccess/order_process/change_order_warehouse';
    const VIEW_ORDER_WAREHOUSE_PERMISSION = 'inventorysuccess/order_process/view_order_warehouse';
    const CREATE_SHIPMENT_WAREHOUSE_PERMISSION = 'inventorysuccess/order_process/view_order_warehouse/create_shipment';
    const CREATE_CREDITMEMO_WAREHOUSE_PERMISSION = 'inventorysuccess/order_process/view_order_warehouse/create_creditmemo';
    const CANCEL_ORDER_WAREHOUSE_PERMISSION = 'inventorysuccess/order_process/view_order_warehouse/cancel_order';

    /**
     * @var Magestore_Inventorysuccess_Model_Service_Permission_PermissionService
     */
    protected $permissionService;

    public function __construct()
    {
        $this->permissionService = Magestore_Coresuccess_Model_Service::permissionService();
    }

    /**
     * Check current user is allowed to change warehouse for order
     *
     * @return bool|mixed
     */
    public function canChangeOrderWarehouse()
    {
        return $this->permissionService->checkPermission(self::CHANGE_WAREHOUSE_PERMISSION);
    }

    /**
     * Check current user is allow to view this warehouse order
     *
     * @param Magestore_Inventorysuccess_Model_Warehouse|null $warehouse
     * @return bool|mixed
     */
    public function canViewWarehouse($warehouse = null)
    {
        return $this->permissionService->checkPermission(self::VIEW_ORDER_WAREHOUSE_PERMISSION, $warehouse);
    }

    /**
     * Get List warehouse are allowed to view by current user
     *
     * @return Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Collection
     */
    public function getViewWarehouseList()
    {
        /** @var Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Collection $warehouseCollection */
        $warehouseCollection = Mage::getResourceModel('inventorysuccess/warehouse_collection');
        if ($this->canChangeOrderWarehouse())
            return $warehouseCollection;
        $this->permissionService->filterPermission($warehouseCollection, self::VIEW_ORDER_WAREHOUSE_PERMISSION);
        return $warehouseCollection;
    }

    /**
     * Get List warehouse are allowed to create shipment by current user
     *
     * @return Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Collection
     */
    public function getShipmentWarehouseList()
    {
        /** @var Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Collection $warehouseCollection */
        $warehouseCollection = Mage::getResourceModel('inventorysuccess/warehouse_collection');
        $this->permissionService->filterPermission($warehouseCollection, self::CREATE_SHIPMENT_WAREHOUSE_PERMISSION);
        return $warehouseCollection;
    }

    /**
     * Check current user is allowed to create shipment for warehouse
     *
     * @param Magestore_Inventorysuccess_Model_Warehouse|null $warehouse
     * @return bool|mixed
     */
    public function canCreateShipment($warehouse = null)
    {
        if ($this->isAllPermission($warehouse))
            return true;
        return $this->permissionService->checkPermission(self::CREATE_SHIPMENT_WAREHOUSE_PERMISSION, $warehouse);
    }

    /**
     * Get List warehouse are allowed to create credit memo by current user
     *
     * @return Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Collection
     */
    public function getCreditmemoWarehouseList()
    {
        /** @var Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Collection $warehouseCollection */
        $warehouseCollection = Mage::getResourceModel('inventorysuccess/warehouse_collection');
        $this->permissionService->filterPermission($warehouseCollection, self::CREATE_CREDITMEMO_WAREHOUSE_PERMISSION);
        return $warehouseCollection;
    }

    /**
     * Check current user is allowed to create credit memo for warehouse
     *
     * @param Magestore_Inventorysuccess_Model_Warehouse|null $warehouse
     * @return bool|mixed
     */
    public function canCreateCreditmemo($warehouse = null)
    {
        if ($this->isAllPermission($warehouse))
            return true;
        return $this->permissionService->checkPermission(self::CREATE_CREDITMEMO_WAREHOUSE_PERMISSION, $warehouse);
    }

    /**
     * Get List warehouse are allowed to cancel order by current user
     *
     * @return Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Collection
     */
    public function getCancelOrderWarehouseList()
    {
        /** @var Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Collection $warehouseCollection */
        $warehouseCollection = Mage::getResourceModel('inventorysuccess/warehouse_collection');
        $this->permissionService->filterPermission($warehouseCollection, self::CANCEL_ORDER_WAREHOUSE_PERMISSION);
        return $warehouseCollection;
    }

    /**
     * Check current user is allowed to cancel order for warehouse
     *
     * @param Magestore_Inventorysuccess_Model_Warehouse|null $warehouse
     * @return bool|mixed
     */
    public function canCancelOrder($warehouse = null)
    {
        if ($this->isAllPermission($warehouse))
            return true;
        return $this->permissionService->checkPermission(self::CANCEL_ORDER_WAREHOUSE_PERMISSION, $warehouse);
    }

    /**
     * Check current user has all permission
     *
     * @param Magestore_Inventorysuccess_Model_Warehouse|null $warehouse
     * @return bool
     */
    public function isAllPermission($warehouse = null)
    {
        if (!$warehouse || !$warehouse->getId())
            return $this->permissionService->checkPermission('Magento_Backend::all');
        return false;
    }
}