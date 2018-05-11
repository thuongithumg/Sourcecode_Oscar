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
 * Adjuststock Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Service_Warehouse_LocationService
    extends Magestore_Inventorysuccess_Model_Service_ProductSelection_ProductSelectionService
{

    /**
     * @var Magestore_Webpos_Model_Userlocation
     */
    protected $_location;

    /**
     * Return array of options as value-label pairs.
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray($locationId = null)
    {
        $mapCollection = Mage::getModel('inventorysuccess/warehouseLocationMap')->getCollection();
        if ($locationId) {
            $mapCollection->addFieldToFilter('location_id', array('neq' => $locationId));
        }
        $warehouseMapIDs = $mapCollection->getAllWarehouseIds();
        $wareHouseCollection = Mage::getModel('inventorysuccess/warehouse')->getCollection();
        if ($warehouseMapIDs) {
            $wareHouseCollection->addFieldToFilter('warehouse_id', array('nin' => $warehouseMapIDs));
        }
        $options = array(
            array('value' => 0, 'label' => Mage::helper('inventorysuccess')->__("Don't link to any warehouse")),
            array('value' => -1, 'label' => Mage::helper('inventorysuccess')->__('Create a new Warehouse'))
        );
        if (is_array($wareHouseCollection->toOptionArray())) {
            $options = array_merge($options, $wareHouseCollection->toOptionArray());
        }
        return $options;
    }

    /**
     * Return array of options as value-label pairs.
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function getAllOptionArray()
    {
        $options = array(
            array('value' => -1, 'label' => Mage::helper('inventorysuccess')->__('Create a new Warehouse'))
        );
        $wareHouseCollection = Mage::getModel('inventorysuccess/warehouse')->getCollection();
        if(is_array($wareHouseCollection->getOptionArray())) {
            $options = array_merge($options, $wareHouseCollection->getOptionArray());
        }
        return $options;
    }

    /**
     * Return array of options as value-label pairs.
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function getWarehouseOptionArray()
    {
        $options = array(
             0 => Mage::helper('inventorysuccess')->__("Don't link to any warehouse"),
            -1 => Mage::helper('inventorysuccess')->__('Create a new Warehouse')
        );
        $wareHouseCollection = Mage::getModel('inventorysuccess/warehouse')->getCollection();
        if(is_array($wareHouseCollection->getOptionArray())) {
            $options = $options + $wareHouseCollection->getOptionArray();
        }
        return $options;
    }

    /**
     * Return array of options as value-label pairs.
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toLocationOptionArray($warehouseId = null)
    {
        if (Mage::helper('core')->isModuleEnabled('Magestore_Webpos')) {
            try {
                $options = array();
                $locationCollection = Mage::getModel('webpos/userlocation')->getCollection();
                $options[] = array('value' => 0, 'label' => Mage::helper('inventorysuccess')
                                    ->__("Don't associate to Location"));
                $options[] = array('value' => -1, 'label' => Mage::helper('inventorysuccess')
                                    ->__('Create a new Location'));
                $mapCollection = Mage::getModel('inventorysuccess/warehouseLocationMap')->getCollection();
                if ($warehouseId) {
                    $mapCollection->addFieldToFilter('warehouse_id', array('neq' => $warehouseId));
                }
                $locationMapIDs = $mapCollection->getAllLocationIds();
                if ($locationMapIDs) {
                    $locationCollection->addFieldToFilter('location_id', array('nin' => $locationMapIDs));
                }
                if(is_array($locationCollection->toOptionArray())) {
                    $options = array_merge($options, $locationCollection->toOptionArray());
                }
                return $options;
            } catch (Exception $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Return array of options as value-label pairs.
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function getLocationOptionArray()
    {
        $options = array(
            0 => Mage::helper('inventorysuccess')->__("Don't associate to Location"),
            -1 => Mage::helper('inventorysuccess')->__('Create a new Location')
        );
        if (Mage::helper('core')->isModuleEnabled('Magestore_Webpos')) {
            try {
                $locationCollection = Mage::getModel('webpos/userlocation')->getCollection();
                foreach ($locationCollection as $item) {
                    $data[$item->getId()] = $item->getDisplayName() ;
                }
                $options = array_merge($options, $data);
            } catch (Exception $e) {
            }
        }
        return $options;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function createListMapping(array $data = array())
    {
        $locationIds = array();
        foreach ($data as $locationId => $item) {
            if (isset($item['warehouse_id'])) {
                $this->mappingWarehouseToLocation($item['warehouse_id'], $locationId, true);
                $locationIds[] = $locationId;
            }
        }
        $allLocationIds = Mage::getModel('inventorysuccess/warehouseLocationMap')->getCollection()->getAllLocationIds();
        $deleteMapping = array_diff($allLocationIds, $locationIds);
        $queryProcessorService = Magestore_Coresuccess_Model_Service::queryProcessorService();
        if (count($deleteMapping)) {
            /* start queries processing */
            $queryProcessorService->start();
            /* prepare to remove mapping, then add queries to Processor */
            $this->_prepareRemoveMapping($deleteMapping);
            /* process queries in Processor */
            $queryProcessorService->process();
        }
        return $this;
    }

    /**
     * @param $warehouseId
     * @param $locationId
     * @param bool $force
     * @return bool
     */
    public function mappingWarehouseToLocation($warehouseId, $locationId, $force = false)
    {
        if ($warehouseId < 1 && $locationId < 1) {
            return false;
        }
        if (Mage::helper('core')->isModuleEnabled('Magestore_Webpos')) {
            try {
                if (!$this->_location) {
                    $this->_location = Mage::getModel('webpos/userlocation');
                }
            } catch (\Exception $ex) {
                return false;
            }
        } else {
            return false;
        }
        $warehouse = Mage::getModel('inventorysuccess/warehouse');

        if ($locationId == 0 || $warehouseId == 0) {
            $oldwarehouseLocationMap = Mage::getModel('inventorysuccess/warehouseLocationMap');
            if ($locationId) {
                $oldwarehouseLocationMap->load($locationId, 'location_id');
            } else {
                $oldwarehouseLocationMap->load($warehouseId, 'warehouse_id');
            }
            $oldwarehouseLocationMap->delete();
        } elseif ($locationId == -1) {
            try {
                $warehouse->load($warehouseId);
                $location = $this->_location;
                $location->setDisplayName($warehouse->getWarehouseName())
                    ->setDescription($warehouse->getWarehouseName())
                    ->setAddress($warehouse->getWarehouseName())
                    ->save();
                $locationId = $location->getId();
            } catch (\Exception $e) {
                return false;
            }
        } elseif ($warehouseId == -1) {
            try {
                $location = $this->_location->load($locationId);
                $newWarehouseCode = $this->getNewWarehouseCode($location->getDisplayName());
                $warehouse->setWarehouseName($location->getDisplayName())
                    ->setWarehouseCode($newWarehouseCode)
                    ->save();
                $warehouseId = $warehouse->getId();
            } catch (\Exception $e) {
                return false;
            }
        }
        if ($locationId > 0 && $warehouseId > 0) {
            $oldwarehouseLocationMap = Mage::getModel('inventorysuccess/warehouseLocationMap');
            $oldwarehouseLocationMap->load($warehouseId, 'warehouse_id');
            try {
                if ($oldwarehouseLocationMap->getLocationId() && $locationId != $oldwarehouseLocationMap->getLocationId() && $force) {
                    $oldwarehouseLocationMap->delete();
                } elseif ($oldwarehouseLocationMap->getLocationId()) {
                    return false;
                }
                $newWarehouseLocationMap = Mage::getModel('inventorysuccess/warehouseLocationMap');
                $newWarehouseLocationMap->load($locationId, 'location_id')
                    ->setLocationId($locationId)
                    ->setWarehouseId($warehouseId)
                    ->save();
            } catch (\Exception $ex) {
                return false;
            }
        } else {
            return false;
        }
        return true;
    }

    /**
     * get new warehouse code
     *
     * @param string $locationName
     * @return string
     */
    protected function getNewWarehouseCode($code)
    {
        $warehouseCode = $code;
        $collection = Mage::getModel('inventorysuccess/warehouse')->getCollection()
                        ->addFieldToFilter(Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_NAME, $code)
                        ->setOrder(Magestore_Inventorysuccess_Model_Warehouse::WAREHOUSE_ID, 'DESC')
        ;
        if($collection->getSize()){
            $warehouse = $collection->getFirstItem();
            $warehouseCode = $code . '_' . $warehouse->getId();
        }
        return $warehouseCode;
    }

    /**
     * @param array $deleteMapping
     * @return $this
     */
    protected function _prepareRemoveMapping($deleteMapping = array())
    {
        $conditions = array();
        if (count($deleteMapping)) {
            $conditions['location_id IN (?)'] = $deleteMapping;
        }
        /* add query to Processor */
        $queryProcessorService = Magestore_Coresuccess_Model_Service::queryProcessorService();
        $queryProcessorService->addQuery(array('type' => $queryProcessorService::QUERY_TYPE_DELETE,
            'condition' => $conditions,
            'table' => Mage::getModel('inventorysuccess/warehouseLocationMap')->getResource()->getMainTable()
        ));
        return $this;
    }

    /**
     * @param $locationId
     * @return Magestore_Inventorysuccess_Model_Mysql4_Warehouse_Product_Collection
     */
    public function getProductIdsByLocationId($locationId)
    {
        $warehouseId = $this->getWarehouseIdByLocationId($locationId);
        $result = Magestore_Coresuccess_Model_Service::warehouseStockService()
                    ->getStocks($warehouseId);
        return $result;
    }

    /**
     * @param $locationId
     * @return $warehouseId
     */
    public function getWarehouseIdByLocationId($locationId)
    {
        $warehouseId = Mage::getModel('inventorysuccess/warehouseLocationMap')
                        ->load($locationId, 'location_id')->getWarehouseId();
        if (!$warehouseId) {
            $warehouseId = Magestore_Coresuccess_Model_Service::warehouseService()
                            ->getPrimaryWarehouse()->getId();
        }
        return $warehouseId;
    }
}