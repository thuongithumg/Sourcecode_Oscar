<?php

/**
 * Class Magestore_Inventorysuccess_Model_Api2_Warehouse_Rest_Admin_V1
 */
class Magestore_Inventorysuccess_Model_Api2_Warehouse_Rest_Admin_V1 extends
    Magestore_Inventorysuccess_Model_Api2_Abstract
{
    const ACTION_TYPE_GETLIST_CREATE  = 'getlist_create';
    const ACTION_TYPE_RETRIEVE_UPDATE = 'retrieve_update';

    public function dispatch()
    {
        switch ( $this->getActionType() ) {
            case self::ACTION_TYPE_GETLIST_CREATE:
                /** GET = get list */
                if ( $this->getRequest()->isGet() ) {
                    $result = $this->getWarehouseCollection();
                } /** POST = create */
                elseif ( $this->getRequest()->isPost() ) {
                    $data   = $this->getRequest()->getBodyParams();
                    $result = $this->createWarehouse($data);
                }
                break;
            case self::ACTION_TYPE_RETRIEVE_UPDATE:
                /** GET = retrieve */
                if ( $this->getRequest()->isGet() ) {
                    $warehouseCode = $this->getRequest()->getParam('warehouseCode');
                    $result        = $this->getWarehouse($warehouseCode);
                } /** PUT = update */
                elseif ( $this->getRequest()->isPut() ) {
                    $warehouseCode = $this->getRequest()->getParam('warehouseCode');
                    $data          = $this->getRequest()->getBodyParams();
                    $result        = $this->updateWarehouse($warehouseCode, $data);
                }
                break;
            default:
                $result = array();
        }
        $this->_render($result);
        $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
    }

    /**
     * get array of all warehouses
     * @return array
     */
    public function getWarehouseCollection()
    {
        /** @var Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Collection $collection */
        $collection = $collection = Mage::getResourceModel('inventorysuccess/warehouse_collection');
        $this->_applyCollectionModifiersNew($collection);
        $this->_applyFilter($collection);
        return $collection->load()->toArray();
    }


    /**
     * get warehouse using warehouse_code
     * @param $warehouseCode
     * @return array
     */
    public function getWarehouse( $warehouseCode )
    {
        $collection = Mage::getResourceModel('inventorysuccess/warehouse_collection')
                          ->addFieldToFilter(Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_CODE, $warehouseCode);
        return $collection->getFirstItem()->getData();
    }

    /**
     * Create new warehouse
     * $data
     *      ['warehouse_code' => '312'],
     * @param array $data
     * @return int
     * @throws
     */
    public function createWarehouse( $data )
    {
        $warehouse = new Varien_Object($data);
        /** @var Magestore_Inventorysuccess_Model_Warehouse $newWarehouse */
        $newWarehouse = Mage::getModel('inventorysuccess/warehouse');

        /** check if warehouse exists */
        if ( $warehouse->getWarehouseCode() ) {
            $newWarehouse->load($warehouse->getWarehouseCode(),
                                Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_CODE);
        }
        if ( $newWarehouse->getWarehouseId() ) {
            throw new \Exception("The warehouse code '" . $newWarehouse->getWarehouseCode() . "' already exists.");
        }

        /** save warehouse */
        try {
            $newWarehouse->setData($data)->save();
            $this->updatePrimaryWarehouse($newWarehouse);
        } catch ( \Exception $e ) {
            throw new \Exception($e->getMessage());
        }
        return $this->getWarehouse($newWarehouse->getWarehouseCode());
    }

    /**
     * @param $warehouseCode
     * @param $data
     * @return mixed
     * @throws Exception
     */
    public function updateWarehouse(
        $warehouseCode,
        $data
    ) {
        /** @var Magestore_Inventorysuccess_Model_Warehouse $warehouse */
        $warehouse = Mage::getModel('inventorysuccess/warehouse')
                         ->load($warehouseCode, Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_CODE);
        $warehouse->addData($data);
        try {
            $warehouse->getResource()->save($warehouse);
            $this->updatePrimaryWarehouse($warehouse);
        } catch ( \Exception $e ) {
            throw new \Exception($e->getMessage());
        }
        return $this->getWarehouse($warehouse->getWarehouseCode());
    }

    /**
     * @return Magestore_Inventorysuccess_Model_Service_Warehouse_WarehouseService
     */
    protected function _warehouseService()
    {
        return Magestore_Coresuccess_Model_Service::warehouseService();
    }

    /**
     * if warehouse is primary, update others
     * @param $newWarehouse
     * @return $this
     */
    protected function updatePrimaryWarehouse( $newWarehouse )
    {
        if ( $newWarehouse->getIsPrimary() ) {
            $collection = Mage::getModel('inventorysuccess/warehouse')->getCollection();
            foreach ( $collection as $warehouse ) {
                if ( $warehouse->getWarehouseCode() != $newWarehouse->getWarehouseCode() && $warehouse->getIsPrimary() ) {
                    $warehouse->setIsPrimary(false);
                    $warehouse->getResource()->save($warehouse);
                }
            }
        }
        return $this;
    }
}
