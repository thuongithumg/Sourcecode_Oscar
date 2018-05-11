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
class Magestore_Webpos_Model_Api2_Pos_Rest_Admin_V1 extends Magestore_Webpos_Model_Api2_Abstract
{
    /**
     *
     */
    const OPERATION_GET_POS_LIST = 'get';
    const OPERATION_ASSIGN_POS = 'update';

    /**
     * @throws Exception
     * @throws Zend_Controller_Response_Exception
     */
    public function dispatch()
    {
        $this->_initStore();
        switch ($this->getActionType()) {
            case self::OPERATION_GET_POS_LIST:
                if(Mage::getStoreConfig('webpos/general/enable_session')) {
                    $result = $this->getPosList();
                } else {
                    $result = $this->getLocationList();
                }
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            case self::OPERATION_ASSIGN_POS:
                $params = $this->getRequest()->getBodyParams();
                $result = $this->assignStaff($params);
                $this->_render($result);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            default:
                $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
                break;
        }
    }

    /**
     * @return mixed
     * @throws Exception
     * @throws Mage_Api2_Exception
     */
    public function getPosList()
    {
        $collection = Mage::getResourceModel('webpos/pos_collection');
        $collection->joinToLocation();
        $filters = $this->getRequest()->getParam('filter');
        if(!empty($filters)){
            foreach ($filters as $filter){
                if(!empty($filter['attribute']) && $filter['attribute'] == 'user_id'){
                    $collection->getAvailablePos(isset($filter['eq']) ? $filter['eq'] : null);
                }
            }
        }

        $pageNumber = $this->getRequest()->getPageNumber();
        if ($pageNumber != abs($pageNumber)) {
            $this->_critical(self::RESOURCE_COLLECTION_PAGING_ERROR);
        }

        $pageSize = $this->getRequest()->getPageSize();
        if ($pageSize) {
            if ($pageSize != abs($pageSize) || $pageSize > self::PAGE_SIZE_MAX) {
                $this->_critical(self::RESOURCE_COLLECTION_PAGING_LIMIT_ERROR);
            }
        }

        $orderField = $this->getRequest()->getOrderField();

        if (null !== $orderField) {
            if ($orderField == 'till_name') {
                $orderField = 'pos_name';
            }
            $collection->setOrder($orderField, $this->getRequest()->getOrderDirection());
        }
        $collection->setCurPage($pageNumber)->setPageSize($pageSize);
        /* @var Varien_Data_Collection_Db $customerCollection */

        $result['items'] = array();
        foreach ($collection as $pos) {
            $data = $pos->getData();
            $data['pos_name'] = $pos->getData('pos_name');
            $data['store_id'] = $pos->getData('location_store_id');
            $data['denominations'] = $pos->getDenominations();
            $result['items'][] = $data;
        }
        $result['total_count'] = $collection->getSize();
        return $result;
    }

    /**
     * @return mixed
     * @throws Exception
     * @throws Mage_Api2_Exception
     */
    public function getLocationList()
    {
        $collection = Mage::getResourceModel('webpos/userlocation_collection');
        $collection->getAvailableLocation();
        $filters = $this->getRequest()->getParam('filter');
        if(!empty($filters)){
            foreach ($filters as $filter){
                if(!empty($filter['attribute']) && $filter['attribute'] == 'user_id'){
                    $collection->getAvailableLocation(isset($filter['eq']) ? $filter['eq'] : null);
                }
            }
        }

        $pageNumber = $this->getRequest()->getPageNumber();
        if ($pageNumber != abs($pageNumber)) {
            $this->_critical(self::RESOURCE_COLLECTION_PAGING_ERROR);
        }

        $pageSize = $this->getRequest()->getPageSize();
        if ($pageSize) {
            if ($pageSize != abs($pageSize) || $pageSize > self::PAGE_SIZE_MAX) {
                $this->_critical(self::RESOURCE_COLLECTION_PAGING_LIMIT_ERROR);
            }
        }

        $orderField = $this->getRequest()->getOrderField();

        if (null !== $orderField) {
            if ($orderField == 'till_name' || $orderField == 'pos_name') {
                $orderField = 'display_name';
            }
            $collection->setOrder($orderField, $this->getRequest()->getOrderDirection());
        }
        $collection->setCurPage($pageNumber)->setPageSize($pageSize);
        /* @var Varien_Data_Collection_Db $customerCollection */

        $result['items'] = array();
        foreach ($collection as $location) {
            $data = $location->getData();
            $data['pos_name'] = $location->getData('display_name');
            $data['location_name'] = $location->getData('display_name');
            $data['store_id'] = $location->getData('location_store_id');
            $result['items'][] = $data;
        }

        $result['total_count'] = $collection->getSize();

        return $result;

    }

    /**
     * get Pos
     *
     */
    public function get($posId)
    {
        $pos = Mage::getModel('webpos/pos')->load($posId);
        if($pos->getId()) {
            return $pos;
        }
        return null;
    }

    /**
     * save Pos
     * @throws \Exception
     */
    public function save($pos)
    {
        try {
            $posModel = Mage::getModel('webpos/pos');
            $posModel->setData($pos);
            $posModel->save();
        }catch (\Exception $e) {
            throw new Exception(Mage::helper('webpos')->__('Unable to save pos'));
        }
        return $pos;
    }


    /**
     * assign staff for pos
     *
     * @param array $params
     * @return boolean
     * @throws Exception
     */
    public function assignStaff($params)
    {
        $posId = $params['pos_id'];
        $locationId = $params['location_id'];
        $currentSessionId = $params['current_session_id'];
        $sessionModel = Mage::getModel('webpos/user_webpossession')->load($currentSessionId, 'session_id');
        if ($sessionModel->getId()) {
            $storeId = $sessionModel->getCurrentStoreId();
            $location = Mage::getModel('webpos/userlocation')->load($locationId);
            if($location->getLocationStoreId()) {
                $storeId = $location->getLocationStoreId();
            }
            $sessionModel->setData('location_id', $locationId);
            $sessionModel->setData('pos_id', $posId);
            $sessionModel->setData('current_store_id', $storeId);
            try {
                $sessionModel->save();
                $staffId = $sessionModel->getStaffId();
                if($staffId && $posId) {
                    Mage::getModel('webpos/pos')->assignStaff($posId, $staffId);
                }
            } catch (Exception $e) {
                throw new Exception(Mage::helper('webpos')->__('Can not connect assign location'));
            }
        }
        return true;

    }

    /**
     * unassign staff in pos
     * @param string $posId
     *
     * @return mixed
     */
    public function unassignStaff($posId)
    {
        $pos =  Mage::getModel('webpos/pos')->load($posId);
        $pos->setUserId(null);
        try {
            $pos->save();
        }catch (Exception $e) {

        }
    }

    /**
     * auto join all user for pos
     * @param string $posId
     *
     * @return mixed
     */
    public function autoJoinAllUsers($posId)
    {
        $userCollection = Mage::getModel('webpos/user')->getCollection();
        foreach ($userCollection as $user){
            $posIds = $user->getPosIds();
            $posIds = explode(',', $posIds);
            $posIds[] = $posId;
            $posIds = array_unique($posIds);
            $posIds = implode(',', $posIds);
            $user->setPosIds($posIds);
            try {
                $user->save();
            } catch (Exception $e) {

            }
        }
    }
}
