<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Helper;

    /**
     * class \Magestore\Webpos\Helper\Permission
     *
     * Web POS Permission helper
     * Methods:
     *  getAllCurrentPermission

     *
     * @category    Magestore
     * @package     Magestore_Webpos
     * @module      Webpos
     * @author      Magestore Developer
     */
/**
 * Class Permission
 * @package Magestore\Webpos\Helper
 */
/**
 * Class Permission
 * @package Magestore\Webpos\Helper
 */
/**
 * Class Permission
 * @package Magestore\Webpos\Helper
 */
class Permission extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magestore\Webpos\Model\Staff\WebPosSessionFactory
     */
    protected $_webposSessionFactory;
    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManager;

    /**
     * @var \Magestore\Webpos\Model\Staff\StaffFactory
     */
    protected $_staffFactory;

    /**
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_timezone;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;


    /**
     * Permission constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magestore\Webpos\Model\Staff\WebPosSessionFactory $sessionFactory
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magestore\Webpos\Model\Staff\StaffFactory $staffFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\Webpos\Model\Staff\WebPosSessionFactory $sessionFactory,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magestore\Webpos\Model\Staff\StaffFactory $staffFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->_objectManager = $objectManager;
        $this->_webposSessionFactory = $sessionFactory;
        $this->_cookieManager = $cookieManager;
        $this->_staffFactory = $staffFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_timezone = $timezone;
        $this->_request = $request;
        $this->checkoutSession = $checkoutSession;
        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getAllCurrentPermission() {
        $staffModel = $this->getCurrentStaffModel();
        $resourceAccess = array();
        if ($staffModel->getId()) {
            $roleId = $staffModel->getRoleId();
            $authorizationCollection = $this->_objectManager->create('Magestore\Webpos\Model\Staff\AuthorizationRule')
                ->getCollection()->addFieldToFilter('role_id', $roleId);
            foreach ($authorizationCollection as $resource) {
                $resourceAccess[] = $resource->getResourceId();
            }
        }
        return $resourceAccess;
    }

    /**
     * @param $resource
     * @return bool
     */
    public function isAllowResource($resource) {
        $allPermission = $this->getAllCurrentPermission();
        if (in_array($resource, $allPermission) || in_array('Magestore_Webpos::all', $allPermission)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return int
     */
    public function getCurrentUser()
    {
        if($this->_coreRegistry->registry('currrent_webpos_staff')) {
            return $this->_coreRegistry->registry('currrent_webpos_staff')->getId();
        }
        $phpSession = $this->_request->getParam('session');
        if(!$phpSession) {
            $phpSession = $this->_cookieManager->getCookie('WEBPOSSESSION');
        }
        $webposModel = $this->_webposSessionFactory->create()->load($phpSession, 'session_id');
        if ($webposModel->getId()) {
            $logTimeStaff = $webposModel->getData('logged_date');
            $currentTime = $this->_timezone->scopeTimeStamp();
            $logTimeStamp = strtotime($logTimeStaff);
            if (($currentTime - $logTimeStamp) <= $this->getTimeoutSession()) {
                return $webposModel->getStaffId();
            } else {
                $webposModel->delete();
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * @param $phpSession
     * @return int
     */
    public function authorizeSession($phpSession)
    {
        $webposModel = $this->_webposSessionFactory->create()->load($phpSession, 'session_id');
        if(!$this->_coreRegistry->registry('currrent_webpos_staff')) {
            $staff = $this->_objectManager->create('Magestore\Webpos\Model\Staff\Staff')->load($webposModel->getStaffId());
            $this->_coreRegistry->register('currrent_webpos_staff', $staff);
        }
        if ($webposModel->getId()) {
            $logTimeStaff = $webposModel->getData('logged_date');
            $currentTime = $this->_timezone->scopeTimeStamp();
            $logTimeStamp = strtotime($logTimeStaff);
            if (($currentTime - $logTimeStamp) <= $this->getTimeoutSession()) {
                $newLoggedDate =  strftime('%Y-%m-%d %H:%M:%S', $this->_timezone->scopeTimeStamp());
                $webposModel->setData('logged_date', $newLoggedDate);
                $webposModel->save();
                return $webposModel->getStaffId();
            } else {
                $webposModel->delete();
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * @param bool $session
     * @return bool
     */
    private function _validateSession($session = false){
        $session = $session = ($session) ? $session : $this->_request->getParam('session');
        if(!$session) {
            $session = $this->_cookieManager->getCookie('WEBPOSSESSION');
        }
        if(!$session) {
            return false;
        }
        $sessionModel = $this->_webposSessionFactory->create()->load($session, 'session_id');
        if(!$this->_coreRegistry->registry('currrent_webpos_staff')) {
            $staff = $this->_objectManager->create('Magestore\Webpos\Model\Staff\Staff')->load($sessionModel->getStaffId());
            $this->_coreRegistry->register('currrent_webpos_staff', $staff);
        }
        if ($sessionModel->getId()) {
            $logTimeStaff = $sessionModel->getData('logged_date');
            $currentTime = $this->_timezone->scopeTimeStamp();
            $logTimeStamp = strtotime($logTimeStaff);
            if (($currentTime - $logTimeStamp) <= $this->getTimeoutSession()) {
                $newLoggedDate =  strftime('%Y-%m-%d %H:%M:%S', $this->_timezone->scopeTimeStamp());
                $sessionModel->setData('logged_date', $newLoggedDate);
                $sessionModel->save();
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
        $user = $this->_staffFactory->create();
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
        if($this->_coreRegistry->registry('currrent_webpos_staff')) {
            return $this->_coreRegistry->registry('currrent_webpos_staff');
        }
        $currentId = $this->getCurrentUser();
        $currentModel = $this->_objectManager->create('Magestore\Webpos\Model\Staff\Staff')
            ->load($currentId);
        return $currentModel;
    }

    /**
     * @return int
     */
    public function getCurrentLocation()
    {
        $staff = $this->getCurrentStaffModel();
        $locationIds = $staff->getLocationId();
        $currentSessionId = $this->_request->getParam('session');
        if(!$currentSessionId) {
            $currentSessionId = $this->_cookieManager->getCookie('WEBPOSSESSION');
        }
        $sessionModel = $this->_webposSessionFactory->create()->load($currentSessionId, 'session_id');
        if ($sessionModel->getId()) {
            $currentLocationId = $sessionModel->getData('location_id');
        } else {
            $currentLocationId = 0;
        }
        $locationIdsArray = explode(',', $locationIds);
        if (count($locationIdsArray) == 1 && !$currentLocationId) {
            $this->checkoutSession->setData('location_id', $locationIdsArray[0]);
            return $locationIdsArray[0];
        } elseif ($currentLocationId){
            return $currentLocationId;
        } else {
            return 0;
        }
    }

    /**
     * @return array
     */
    public function getAllLocationIds()
    {
        $staff = $this->getCurrentStaffModel();
        $locationIds = $staff->getLocationId();
        $locationIdsArray = explode(',', $locationIds);
        $result = array();
        foreach ($locationIdsArray as $locationId) {
            $locationModel = $this->_objectManager->create('Magestore\Webpos\Model\Location\Location')->load($locationId);
            $result[] = array(
                'value' => $locationId,
                'text' => $locationModel->getDisplayName()
            );
        }
        return $result;
    }

    /**
     * Get current location object
     *
     * @return \Magestore\Webpos\Model\Location\Location
     */
    public function getCurrentLocationObject()
    {
        $locationId = $this->getCurrentLocation();
        $locationModel = $this->_objectManager->create('Magestore\Webpos\Model\Location\Location')->load($locationId);

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
        return $this->scopeConfig->getValue('webpos/general/session_timeout', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
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
            $role = $this->_objectManager->create('Magestore\Webpos\Model\Staff\Role')->load($roleId);
            if($role->getId()){
                $maximumDiscount = ($role->getData("maximum_discount_percent"))?$role->getData("maximum_discount_percent"):100;
            }
        }
        return $maximumDiscount;
    }

    /**
     * check session validation
     *
     * return boolean
     */
    public function validateRequestSession()
    {
        $session = $this->_request->getParam('session');
        return $this->authorizeSession($session);
    }

    /**
     * @return bool
     */
    public function getCurrentSessionModel(){
        $session = $this->_request->getParam('session');
        $phpSession = $this->_validateSession(($session)?$session:false);
        return ($phpSession)?$phpSession:false;
    }

    /**
     * @return bool
     */
    public function getCurrentSession(){
        $session = $this->getCurrentSessionModel();
        return ($session)?$session->getSessionId():false;
    }

    /**
     * @return bool
     */
    public function getCurrentStoreId(){
        $session = $this->getCurrentSessionModel();
        return ($session)?$session->getCurrentStoreId():false;
    }

    /**
     * @return bool
     */
    public function getCurrentQuoteId(){
        $session = $this->getCurrentSessionModel();
        return ($session)?$session->getCurrentQuoteId():false;
    }

    /**
     * @return bool
     */
    public function getCurrentShiftId(){
        $session = $this->getCurrentSessionModel();
        return ($session)?$session->getCurrentShiftId():false;
    }

    /**
     * @return bool
     */
    public function isShowChoosePosLocation()
    {
        $currentSessionId = $this->_request->getParam('session');
        if(!$currentSessionId) {
            $currentSessionId = $this->_cookieManager->getCookie('WEBPOSSESSION');
        }
        $sessionModel = $this->_webposSessionFactory->create()->load($currentSessionId, 'session_id');
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
        return $this->scopeConfig->getValue('webpos/general/enable_session');
    }

    public function getCurrentPosId()
    {
        $currentSessionId = $this->_request->getParam('session');
        if(!$currentSessionId) {
            $currentSessionId = $this->_cookieManager->getCookie('WEBPOSSESSION');
        }
        $sessionModel = $this->_webposSessionFactory->create()->load($currentSessionId, 'session_id');
        if ($sessionModel->getId()) {
            return $sessionModel->getPosId();
        } else {
            return null;
        }
    }
}
