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
 * Inventorysuccess Service
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Service_Permission_PermissionService
{
    /**
     * @var Magestore_Coresuccess_Model_Service_QueryProcessorService
     */
    protected $queryProcessorService;

    /**
     * Magestore_Inventorysuccess_Model_Service_Permission_PermissionService constructor.
     */
    public function __construct()
    {
        $this->queryProcessorService = Magestore_Coresuccess_Model_Service::queryProcessorService();
    }
    
    /**
     * 
     * @param Mage_Admin_Model_Rules $rule
     * @return boolean
     */
    public function isAllowed($rule)
    {
        if (version_compare(Mage::getVersion(), '1.8.1.0', '>')) {
            return $rule->isAllowed();
        } else {
            return $rule->getPermission();
        }         
    }

    /**
     * @param int $roleId
     * @param string $resourceId
     * @return bool
     */
    public function isAllow($roleId, $resourceId){
        $isAllow = false;
        $isCheckAllPermission = false;
        $rules_set = Mage::getResourceModel('admin/rules_collection')->getByRoles($roleId)->load();
        
        foreach ($rules_set->getItems() as $item) {
            if ($item->getResourceId() == 'admin') {
                $isCheckAllPermission = true;              
                if($this->isAllowed($item)) {
                    return true;
                }
            }
            if ($resourceId == $item->getResourceId() || 'admin/' . $resourceId == $item->getResourceId()) {
                if ($isCheckAllPermission) {
                    return $this->isAllowed($item);
                } else {
                    $isAllow = $this->isAllowed($item);
                }
            }
        }
        return $isAllow;
    }

    /**
     * @param $resourceId
     * @param Mage_Core_Model_Abstract|null $object
     * @param null $staffId
     * @return bool
     */
    public function checkPermission($resourceId, Mage_Core_Model_Abstract $object = null, $staffId = null)
    {
        if (!$staffId) {
            $staffId = Mage::getSingleton('admin/session')->getUser()->getId();
        }
        if (!$object) {
            return Mage::getSingleton('admin/session')->isAllowed($resourceId);
        }
        if (Mage::getSingleton('admin/session')->isAllowed('all')) {
            return true;
        }
        if (!Mage::getSingleton('admin/session')->isAllowed($resourceId)) {
            return false;
        }
        $permissionModel = $this->loadPermissionByObject($object, $staffId);
        if ($permissionModel->getRoleId() && $resourceId) {
            return $this->isAllow($permissionModel->getRoleId(), $resourceId);
        } else {
            return false;
        }
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @param int $staffId
     * @return false|Mage_Core_Model_Abstract
     */
    public function loadPermissionByObject(Mage_Core_Model_Abstract $object, $staffId)
    {
        if (!$object->getId() || !$object->getPermissionType() || !$staffId) {
            return Mage::getModel('inventorysuccess/permission');
        }
        $collection = $this->getListPermissionsByObject($object, $staffId);
        if ($collection->getSize()) {
            return $collection->getFirstItem();
        } else {
            return Mage::getModel('inventorysuccess/permission');
        }
    }

    /**
     * @param Mage_Core_Model_Resource_Db_Collection_Abstract $collection
     * @param $resourceId
     * @param null $staffId
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    public function filterPermission(
        Mage_Core_Model_Resource_Db_Collection_Abstract $collection, $resourceId, $staffId = null
    )
    {
        if (!$this->checkPermission('all')) {
            if (!$staffId) {
                $staffId = Mage::getSingleton('admin/session')->getUser()->getId();
            }
            $collection->addFieldToFilter(
                'main_table.' . $collection->getIdFieldName(),
                array('in' => $this->getObjectAllIDsByAction($resourceId, $collection->getNewEmptyItem(), $staffId))
            );
        }
        return $collection;
    }

    /**
     * @param $resourceId
     * @param Mage_Core_Model_Abstract $object
     * @param null $staffId
     * @return array
     */
    public function getObjectAllIDsByAction($resourceId, Mage_Core_Model_Abstract $object, $staffId = null)
    {
        $objectIDs = $this->getObjectAllIDs($object, $staffId);
        $results = array();
        foreach ($objectIDs as $objectId) {
            if ($this->checkPermission($resourceId, $object->load($objectId), $staffId)) {
                $results[] = $objectId;
            }
        }
        return $results;
    }

    /**
     * @param Varien_Object $object
     * @param null $staffId
     * @return mixed
     */
    public function getObjectAllIDs(Mage_Core_Model_Abstract $object, $staffId = null)
    {
        if (!$staffId) {
            $staffId = Mage::getSingleton('admin/session')->getUser()->getId();
        }
        return $this->getListPermissionsByObject($object, $staffId)->getAllObjectIDs();
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @param int $staffId
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    public function getListPermissionsByObject(Mage_Core_Model_Abstract $object, $staffId = null)
    {
        $collection = Mage::getResourceModel('inventorysuccess/permission_collection');
        if ($object->getPermissionType()) {
            $collection->addFieldToFilter(
                Magestore_Inventorysuccess_Model_Permission::OBJECT_TYPE,
                $object->getPermissionType()
            );
        }
        if ($object->getId()) {
            $collection->addFieldToFilter(Magestore_Inventorysuccess_Model_Permission::OBJECT_ID, $object->getId());
        }
        if ($staffId) {
            $collection->addFieldToFilter(Magestore_Inventorysuccess_Model_Permission::USER_ID, $staffId);
        }
        return $collection;
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @param int $staffId
     * @return $this
     */
    public function removePermissionsByObject(Mage_Core_Model_Abstract $object, $staffId = null)
    {
        /* start queries processing */
        $this->queryProcessorService->start();

        /* prepate to remove objectIds from Permission, then add queries to Processor */
        $this->_prepareRemovePermissionsByObject($object->getPermissionType(), $object->getId(), $staffId);

        /* process queries in Processor */
        $this->queryProcessorService->process();

        return $this;
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @param null $staffId
     * @param $data
     * @return $this
     */
    public function setPermissionsByObject(Mage_Core_Model_Abstract $object, $staffId = null, $data)
    {
        /* start queries processing */
        $this->queryProcessorService->start();

        /* prepare to add objectIds to Permission, then add queries to Processor */
        $this->_prepareAddPermissionsByObject($object->getPermissionType(), $object->getId(), $staffId, $data);

        /* process queries in Processor */
        $this->queryProcessorService->process();

        return $this;
    }

    /**
     * @param $objectType
     * @param null $objectId
     * @param null $staffId
     * @return $this
     */
    protected function _prepareRemovePermissionsByObject($objectType, $objectId = null, $staffId = null)
    {
        $conditions = array(Magestore_Inventorysuccess_Model_Permission::OBJECT_TYPE . ' = ?' => $objectType);
        if ($objectId) {
            $conditions[Magestore_Inventorysuccess_Model_Permission::OBJECT_ID . ' = ?'] = $objectId;
        }
        if ($staffId) {
            $conditions[Magestore_Inventorysuccess_Model_Permission::USER_ID . ' = ?'] = $staffId;
        }
        /* add query to Processor */
        $this->queryProcessorService->addQuery(array(
            'type' => Magestore_Coresuccess_Model_Service_QueryProcessorService::QUERY_TYPE_DELETE,
            'condition' => $conditions,
            'table' => Mage::getResourceModel('inventorysuccess/permission')->getMainTable()
        ));
        return $this;
    }

    /**
     * @param $objectType
     * @param null $objectId
     * @param null $staffId
     * @param $data
     * @return bool
     */
    protected function _prepareAddPermissionsByObject($objectType, $objectId = null, $staffId = null, $data)
    {
        /* add new objectIDs to Permission */
        if (!count($data)) {
            return false;
        }

        $insertData = array();
        foreach ($data as $item) {
            $permissionData = array();
            $permissionData[Magestore_Inventorysuccess_Model_Permission::OBJECT_TYPE] = $objectType;
            if ($staffId) {
                $permissionData[Magestore_Inventorysuccess_Model_Permission::USER_ID] = $staffId;
            } else {
                $permissionData[Magestore_Inventorysuccess_Model_Permission::USER_ID] = $item['user_id'];
            }
            if ($objectId) {
                $permissionData[Magestore_Inventorysuccess_Model_Permission::OBJECT_ID] = $objectId;
            } else {
                $permissionData[Magestore_Inventorysuccess_Model_Permission::OBJECT_ID] = $item['object_id'];
            }
            $permissionData[Magestore_Inventorysuccess_Model_Permission::ROLE_ID] = $item['role_id'];

            $insertData[] = $permissionData;
        }
        /* add query to the processor */
        $this->queryProcessorService->addQuery(array(
            'type' => Magestore_Coresuccess_Model_Service_QueryProcessorService::QUERY_TYPE_INSERT,
            'values' => $insertData,
            'table' => Mage::getResourceModel('inventorysuccess/permission')->getMainTable()
        ));

        return true;
    }

    /**
     * @param Mage_Core_Model_Abstract $object
     * @param null $staffId
     * @param $data
     * @return $this
     */
    public function updatePermissionsByObject(Mage_Core_Model_Abstract $object, $staffId = null, $data)
    {
        foreach ($data as $key => $value) {
            try {
                if ($object->getId() && !$staffId) {
                    $permission = $this->loadPermissionByObject($object, $key);
                    $permissionId = $permission->getId();
                    $permission->setData($value)->setId($permissionId)->save();
                } else {
                    $permission = $this->loadPermissionByObject($object->load($key), $staffId);
                    $permissionId = $permission->getId();
                    $permission->setData($value)->setId($permissionId)->save();
                    $permission->setId(null);
                }
            } catch (Exception $e) {
            }
        }
        return $this;
    }
}