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

class Magestore_Webpos_Model_Observer_Inventorysuccess_WebposAddItemFromShoppingCartBefore
    extends Magestore_Webpos_Model_Observer_Abstract
{
    /**
     * @return boolean
     */
    public function allowToAssignProductAutomatically(){
        $dataObject = new Varien_Object();
        $dataObject->setData('is_automatically', false);
        $this->_dispatchEvent('inventorysuccess_allow_to_assign_product_automatically', array('config' => $dataObject));
        return $dataObject->getData('is_automatically');
    }

    /**
     * Assign product to current warehouse if not existed
     * @param $observer
     */
    public function execute($observer)
    {
        $item = $observer->getData('item');
        if ($this->_helper->isInventorySuccessEnable() && $item) {
            $posWarehouseId = $this->getCurrentWarehouseId();
            if($posWarehouseId){
                $childrens = $item->getChildren();
                $parentItemId = $item->getParentItemId();
                if($parentItemId || !$childrens){
                    $productId = $item->getProduct()->getId();
                    $this->assignProductToWarehouse($productId, $posWarehouseId);
                }elseif($childrens){
                    foreach ($childrens as $children){
                        $productId = $children->getProduct()->getId();
                        $this->assignProductToWarehouse($productId, $posWarehouseId);
                    }
                }
            }
        }
    }

    /**
     * Get current warehouse id for pos location
     * @return string
     */
    public function getCurrentWarehouseId(){
        $locationId = $this->_getHelper('webpos/permission')->getCurrentLocation();
        $locationMapping = Magestore_Coresuccess_Model_Service::locationService();
        $warehouseId = $locationMapping->getWarehouseIdByLocationId($locationId);
        return $warehouseId;
    }

    /**
     * Assign product to warehouse
     * @param $productId
     * @param $warehouseId
     */
    public function assignProductToWarehouse($productId, $warehouseId){
        if($warehouseId && $productId && $productId != 'custom_item'){
            $stockRegistryService = Magestore_Coresuccess_Model_Service::stockRegistryService();
            $stock = $stockRegistryService->getStock($warehouseId, $productId);
            $stockData = $stock->getData();
            if(!$stock || empty($stockData)){
                $isAutoAssign = $this->allowToAssignProductAutomatically();
                ini_set('display_errors', 1);
                if($isAutoAssign){
                    $stockChangeService = Magestore_Coresuccess_Model_Service::stockChangeService();
                    $stockChangeService->change($warehouseId, $productId, 0);
                }else{
                    $warehouseService = Magestore_Coresuccess_Model_Service::warehouseService();
                    $warehouses = $warehouseService->getWarehouses(array($warehouseId));
                    $warehouseName = '';
                    if(!empty($warehouses)){
                        foreach ($warehouses as $warehouse){
                            $warehouseName = $warehouse['warehouse_name'];
                        }
                    }
                    $this->_throwException($this->_helper->__("The product '%s' does not exist in %s", $productId, $warehouseName));
                }
            }
        }
    }
}