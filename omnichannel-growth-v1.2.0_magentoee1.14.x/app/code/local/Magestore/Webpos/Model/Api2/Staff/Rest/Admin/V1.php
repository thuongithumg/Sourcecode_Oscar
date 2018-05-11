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

class Magestore_Webpos_Model_Api2_Staff_Rest_Admin_V1 extends Mage_Api2_Model_Resource
{

    /**#@+
     *  Action types
     */
    const ACTION_TYPE_ENTITY_LOGIN = 'entity_login';
    const ACTION_TYPE_ENTITY_LOGOUT = 'entity_logout';
    const ACTION_TYPE_ENTITY_CHANGEPASSWORD = 'entity_changepassword';
    const OPERATION_GET_STAFF_LIST = 'get';
    /**#@-*/

    /**
     * Retrieve information about staff
     *
     * @throws Mage_Api2_Exception
     * @return array|bool
     */
    protected function login()
    {
        $requestData = $this->getRequest()->getBodyParams();

        $username = $requestData['staff']['username'];
        $password = $requestData['staff']['password'];
        $store = empty($requestData['store'])? false : $requestData['store'];
        if(!$store) {
            $store = Mage::app()
                ->getWebsite(true)
                ->getDefaultGroup()
                ->getDefaultStoreId();
        }
        if ($username && $password) {
            try {
                $resultLogin = Mage::helper('webpos/permission')->login($username, $password);
                if ($resultLogin != 0) {
                    $data = array();
                    $data['current_store_id'] = $store;
                    $data['staff_id'] = $resultLogin;
                    Mage::getSingleton("core/session")->renewSession();
                    $data['session_id'] = Mage::getSingleton("core/session")->getEncryptedSessionId();
                    $data['logged_date'] = strftime('%Y-%m-%d %H:%M:%S', Mage::getModel('core/date')->gmtTimestamp());

                    $webpossession = Mage::getModel('webpos/user_webpossession');
                    $webpossession->setData($data);
                    $cashDrawers = $webpossession->getAvailableCashDrawer();
                    if(!empty($cashDrawers) && count($cashDrawers) == 1){
                        foreach ($cashDrawers as $id => $name){
                            $webpossession->setCurrentTillId($id);
                        }
                    }
                    $insertid = $webpossession->save()->getId();
                    $tillId = $webpossession->getCurrentTillId();

                    if(isset($insertid)){
                        $response = array(
                            'webpos_config' => Mage::helper('webpos')->getWebposConfig($data['session_id']),
                            'webpos_data' => Mage::getModel('webpos/dataManager')->getWebposData($data['session_id']),
                            'session_id' => $data['session_id'],
                            'location_id' => $webpossession->getStaffLocationId(),
                            'available_tills' => $cashDrawers,
                            'store_url' => Mage::getModel('core/store')->load($data['current_store_id'])->getUrl('webpos/index/index', array('_secure' => true))
                        );
                        if(!empty($tillId)) {
                            $response['till_id'] = $tillId;
                        }
                        return $response;
                    }else{
                        return false;
                    }
                } else {
                    return false;
                }
            } catch (Mage_Core_Exception $e) {
                Mage::log($e->getMessage());
                return $e->getMessage();
            }catch (Exception $e) {
                Mage::log($e->getMessage());
                return $e->getMessage();
            }
        }
        return false;
    }

    /**
     *
     * @return string
     */
    public function logout()
    {
        $sessionId = $this->getRequest()->getParam('session');
        $sessionLoginCollection = Mage::getModel('webpos/user_webpossession')->getCollection()->addFieldToFilter('session_id', $sessionId);
        foreach ($sessionLoginCollection as $sessionLogin) {
            $sessionLogin->delete();
            $posId = $sessionLogin->getPosId();
            if($posId){
                $pos = Mage::getModel('webpos/pos')->load($posId);
                $pos->setUserId(null);
                try {
                    $pos->save();
                } catch (Exception $e) {

                }
            }
        }
        return true;
    }

    /**
     * @return string
     */
    public function changepassword()
    {
        $params = $this->getRequest()->getBodyParams();
        $staff = $params['staff'];
        $staffModel = Mage::getModel('webpos/user')->load(Mage::helper('webpos/permission')->getCurrentUser());
        $result = array();
        if (!$staffModel->getId()) {
            $result['error'] = '401';
            $result['message'] = __('There is no staff!');
            return \Zend_Json::encode($result);
        }
        $staffModel->setDisplayName($staff['username']);
        $oldPassword = $staffModel->getPassword();
        if ($staffModel->validatePassword($staff['old_password'])) {
            if ($staff['password']) {
                $staffModel->setNewPassword($staff['password']);
            }
        } else {
            $result['error'] = '1';
            $result['message'] = __('Old password is incorrect!');
            return \Zend_Json::encode($result);
        }
        try {
            $staffModel->save();
            $newPassword = $staffModel->getPassword();
            if ($newPassword != $oldPassword) {
                $sessionParam = $this->getRequest()->getParam('session');
                $userSession = Mage::getModel('webpos/user_webpossession')->getCollection()
                    ->addFieldToFilter('staff_id', array('eq' => $staffModel->getId()))
                    ->addFieldToFilter('session_id', array('neq' => $sessionParam));
                foreach ($userSession as $session) {
                    $session->delete();
                }
            }
        } catch (Exception $e) {
            $result['error'] = '1';
            $result['message'] = $e->getMessage();
            return Zend_Json::encode($result);
        }
        $result['error'] = '0';
        $result['message'] = __('Your account is saved successfully!');
        return Zend_Json::encode($result);
    }

    /**
     * @return mixed
     * @throws Exception
     * @throws Mage_Api2_Exception
     */
    public function getListStaff()
    {
        $collection = Mage::getResourceModel('webpos/user_collection');
        $collection->addFieldTofilter('status', 1);
//        $collection->join(
//            array('role' => 'webpos/role'),
//            'main_table.role_id = role.role_id',
//            array('role_name' => 'role.display_name')
//        );
        $filters = $this->getRequest()->getParam('filter');
        if(!empty($filters)){
            foreach ($filters as $filter){
                $collection->addFieldToFilter($filter['attribute'], $filter['value']);
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
//Zend_Debug::dump($collection->getSize());die;
        $orderField = $this->getRequest()->getOrderField();

        if (null !== $orderField) {
            $collection->setOrder($orderField, $this->getRequest()->getOrderDirection());
        }
        $collection->setCurPage($pageNumber)->setPageSize($pageSize);
        /* @var Varien_Data_Collection_Db $customerCollection */

        $staffArray = array();
        foreach ($collection as $staff) {
//            $staffNormalData = $staff->getData();
            $staffNormalData['staff_id'] = $staff->getData('user_id');
            $staffNormalData['display_name'] = $staff->getData('display_name');
            $staffNormalData['pin'] = $this->encryptPin($staff->getData('pin'));
            $roleId = $staff->getData('role_id');
            $role =  Mage::getModel('webpos/role')->load($roleId);
            $staffNormalData['role'] = $role->getData('display_name');
            $staffNormalData['permission'] = $this->getPermission($role);
            $staffArray[] = $staffNormalData;
        }

        $result['items'] = $staffArray;
        $result['total_count'] = count($staffArray);

        return $result;
    }

    /**
     * get permission
     * @param $role
     * @return mixed
     */
    public function getPermission($role)
    {
        $resourceAccess = array();
        $authorizeRuleCollection = explode(',',$role->getPermissionIds());
        $roleOptionsArray = $role->getOptionArray();
        foreach ($authorizeRuleCollection as $authorizeRule) {
            if (array_key_exists($authorizeRule,$roleOptionsArray)) {
                $resourceAccess[] = $roleOptionsArray[$authorizeRule];
            }
        }
        return $resourceAccess;
    }

    /**
     * encrypt pin code
     * @param string $pin
     * @return mixed
     */
    public function encryptPin($pin)
    {
        $encryptPin = md5(md5($pin).'zxcpoi');
        return $encryptPin;
    }

    public function dispatch()
    {
        switch ($this->getActionType() . $this->getOperation()) {
            /* Create */
            case self::ACTION_TYPE_ENTITY_LOGIN . self::OPERATION_CREATE:
                $this->_errorIfMethodNotExist('login');
                $retrievedData = $this->login();
                $this->_render($retrievedData);
                break;
            case self::ACTION_TYPE_ENTITY_LOGOUT . self::OPERATION_CREATE:
                $this->_errorIfMethodNotExist('logout');
                $retrievedData = $this->logout();
                $this->_render($retrievedData);
                break;
            case self::ACTION_TYPE_ENTITY_CHANGEPASSWORD . self::OPERATION_CREATE:
                $this->_errorIfMethodNotExist('changepassword');
                $retrievedData = $this->changepassword();
                $this->_render($retrievedData);
                break;
            case self::OPERATION_GET_STAFF_LIST . self::OPERATION_RETRIEVE:
                $retrievedData = $this->getListStaff();
                $this->_render($retrievedData);
                $this->getResponse()->setHttpResponseCode(Mage_Api2_Model_Server::HTTP_OK);
                break;
            default:
                $this->_critical(self::RESOURCE_METHOD_NOT_IMPLEMENTED);
                break;
        }
    }

}
