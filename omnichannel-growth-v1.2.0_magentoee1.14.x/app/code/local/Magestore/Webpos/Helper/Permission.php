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

/**
 * Created by PhpStorm.
 * User: Quoc Viet
 * Date: 08/07/2015
 * Time: 8:40 SA
 */
class Magestore_Webpos_Helper_Permission extends Mage_Core_Helper_Abstract
{

    protected function _getSession() {
        return Mage::getSingleton('webpos/user_webpossession');
    }
    /**
     * @return int
     */
    public function getCurrentUser()
    {
        if(Mage::registry('currrent_webpos_staff')) {
            return Mage::registry('currrent_webpos_staff')->getId();
        }
        $sessionToken = Mage::app()->getRequest()->getParam('session');
        $phpSession = $this->_validateSession(($sessionToken)?$sessionToken:false);
        return ($phpSession)?$phpSession->getStaffId():0;
    }

    public function getCurrentSession(){
        $sessionToken = Mage::app()->getRequest()->getParam('session');
        $phpSession = $this->_validateSession(($sessionToken)?$sessionToken:false);
        return ($phpSession)?$phpSession->getSessionId():false;
    }

    public function getCurrentSessionModel(){
        $sessionToken = Mage::app()->getRequest()->getParam('session');
        $phpSession = $this->_validateSession(($sessionToken)?$sessionToken:false);
        return ($phpSession)?$phpSession:false;
    }

    private function _validateSession($session = false){
        $session = $this->_getRequest()->getParam('session');
        if(!$session) {
            $session = ($session)?$session:Mage::app()->getCookie()->get('WEBPOSSESSION');
        }
        if(!$session) {
            return false;
        }
        $sessionModel = Mage::getModel('webpos/user_webpossession')->load($session, 'session_id');
        if ($sessionModel->getId()) {
            $logTimeStaff = $sessionModel->getData('logged_date');
            $currentTime = Mage::getModel('core/date')->gmtTimestamp();
            $logTimeStamp = strtotime($logTimeStaff);
            if (($currentTime - $logTimeStamp) <= $this->getTimeoutSession()) {
                return $sessionModel;
            } else {
                $sessionModel->delete();
                return false;
            }
        } else {
            return false;
        }
    }

    public function validateRequestSession(){
        $session = Mage::app()->getRequest()->getParam('session');
        $sessionModel = Mage::getModel('webpos/user_webpossession')->load($session, 'session_id');
        if ($sessionModel->getId()) {
            $logTimeStaff = $sessionModel->getData('logged_date');
            $currentTime = Mage::getModel('core/date')->gmtTimestamp();
            $logTimeStamp = strtotime($logTimeStaff);
            if (($currentTime - $logTimeStamp) <= $this->getTimeoutSession()) {
                return $sessionModel;
            } else {
                $sessionModel->delete();
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     *
     * @param string $username
     * @param string $password
     * @return string|boolean
     */
    public function login($username, $password) {
        $user = Mage::getModel('webpos/user');
        if ($user->authenticate($username, $password)) {
            return $user->getId();
        }
        return 0;
    }

    /**
     * @return null
     */
    public function getCurrentStaffModel()
    {
        $currentId = $this->getCurrentUser();
        $currentModel = Mage::getModel('webpos/user')->load($currentId);
        return $currentModel;
    }

    /**
     * @return int
     */
    public function getCurrentLocation()
    {
        $staff = $this->getCurrentStaffModel();
        $locationIds = $staff->getLocationId();
        $currentSessionId = $this->_getRequest()->getParam('session');
        if(!$currentSessionId) {
            $currentSessionId = Mage::app()->getCookie()->get('WEBPOSSESSION');
        }
        $sessionModel = Mage::getModel('webpos/user_webpossession')->load($currentSessionId, 'session_id');
        if ($sessionModel->getId()) {
            $currentLocationId = $sessionModel->getData('location_id');
        } else {
            $currentLocationId = 0;
        }
        $locationIdsArray = explode(',', $locationIds);
        if (count($locationIdsArray) == 1 && !$currentLocationId) {
            return $locationIdsArray[0];
        } elseif ($currentLocationId){
            return $currentLocationId;
        } else {
            return 0;
        }
    }

    /**
     * Get current location object
     *
     */
    public function getCurrentLocationObject()
    {
        $locationId = $this->getCurrentLocation();
        $locationModel = Mage::getModel('webpos/userlocation')->load($locationId);

        return $locationModel;
    }


    /**
     * @return int
     */
    public function getCurrentLastOfflineOrderId()
    {
        $staff = $this->getCurrentStaffModel();

        return $staff->getLastOfflineId();
    }

    /**
     * @return int
     */
    public function getTimeoutSession(){
        $settingTimeOut = Mage::getStoreConfig('webpos/general/session_timeout', Mage::app()->getStore()->getId());
        return $settingTimeOut > 0 ? $settingTimeOut : '86400';
    }

    /**
     * Get maximum discount percent
     *
     * @return float
     */
    public function getMaximumDiscountPercent()
    {
        $maximumDiscount = 100;
        $staff = $this->getCurrentStaffModel();
        $roleId = $staff->getRoleId();
        if($roleId){
            $role = Mage::getModel('webpos/role')->load($roleId);
            if($role->getId()){
                $maximumDiscount = ($role->getData("maximum_discount_percent")) ? $role->getData("maximum_discount_percent") :100;
            }
        }
        return $maximumDiscount;
    }


    /**
     * @return bool
     */
    public function getCurrentShiftId(){
        $staffId = $this->getCurrentUser();
        $shiftCollection = Mage::getModel('webpos/shift')->getCollection();
        //$shiftCollection->addFieldToFilter("staff_id", $staffId); //don't need filter because one pos has one shift
        $shiftCollection->addFieldToFilter("pos_id", $this->getCurrentPosId());
        $shiftCollection->addFieldToFilter("status", 0);
        $shiftCollection->setOrder("entity_id","DESC");
        $currentShift = $shiftCollection->setPageSize(1)->setCurPage(1)->getFirstItem();

        if ($currentShift){
            return $currentShift->getShiftId();
        }
        else {
            return 0;
        }
    }

    /**
     * @return int
     */
    public function getCurrentStoreId()
    {
        $session = $this->getCurrentSessionModel();
        return ($session)?$session->getCurrentStoreId():Mage::app()->getStore()->getStoreId();
    }

    /**
     * @return bool
     */
    public function isShowChoosePosLocation()
    {
        $currentSessionId = $this->_getRequest()->getParam('session');
        if(!$currentSessionId) {
            $currentSessionId = Mage::app()->getCookie()->get('WEBPOSSESSION');
        }
        $sessionModel = Mage::getModel('webpos/user_webpossession')->load($currentSessionId, 'session_id');
        if ($sessionModel->getId()) {
            $locationId =  $sessionModel->getData('location_id');
            $posId =  $sessionModel->getData('pos_id');
            $posSelect = $this->isNeedSessionBeforeWorking();
            if (($locationId && !$posSelect) || $posId) {
                return false;
            } else {
                return true;
            }
        }
        return true;
    }

    /**
     * @return mixed
     */
    public function isNeedSessionBeforeWorking()
    {
        return Mage::getStoreConfig('webpos/general/enable_session');
    }

    public function getCurrentPosId()
    {
        $currentSessionId = $this->_getRequest()->getParam('session');
        if(!$currentSessionId) {
            $currentSessionId = Mage::app()->getCookie()->get('WEBPOSSESSION');
        }
        $sessionModel = Mage::getModel('webpos/user_webpossession')->load($currentSessionId, 'session_id');
        if ($sessionModel->getId()) {
            return $sessionModel->getPosId();
        } else {
            return null;
        }
    }
}
