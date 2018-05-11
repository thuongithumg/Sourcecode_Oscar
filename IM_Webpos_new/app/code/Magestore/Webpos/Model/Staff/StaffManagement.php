<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Staff;

/**
 * Customer repository.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
use Magento\Framework\Exception\StateException;
use Magestore\Webpos\Helper\Permission;
use Magestore\Webpos\Api\Data\Staff\StaffSearchResultsInterfaceFactory as SearchResultFactory;
use Magento\Framework\Api\SortOrder;

/**
 * Class StaffManagement
 * @package Magestore\Webpos\Model\Staff
 */
class StaffManagement implements \Magestore\Webpos\Api\Staff\StaffManagementInterface
{
    /**
     * Request
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;
    /**
     * @var \Magestore\Webpos\Model\WebPosSession
     */
    protected $_session;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_timezone;
    /**
     * @var WebPosSession
     */
    protected $_webPosSession;

    /**
     * @var Permission
     */
    protected $_permissionHelper;

    /**
     * @var \Magestore\Webpos\Model\Staff|StaffFactory
     */
    protected $_staffFactory;

    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    protected $_sessionManager;

    /**
     * @var  SearchResultFactory
     */
    protected $_searchResultFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;
    /**
     * @var \Magestore\Webpos\Model\Pos\PosFactory
     */
    protected $posFactory;
    /**
     * @var \Magestore\Webpos\Model\Location\LocationFactory
     */
    protected $locationFactory;


    /**
     * StaffManagement constructor.
     * @param \Magestore\Webpos\Model\WebPosSession $session
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param WebPosSession $webPosSession
     * @param Permission $webposPermission
     * @param StaffFactory $staff
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Session\SessionManager $sessionManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param SearchResultFactory $searchResultFactory
     * @param \Magestore\Webpos\Model\Pos\PosFactory $posFactory
     * @param \Magestore\Webpos\Model\Location\LocationFactory $locationFactory
     */
    public function __construct(
        \Magestore\Webpos\Model\WebPosSession $session,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magestore\Webpos\Model\Staff\WebPosSession $webPosSession,
        \Magestore\Webpos\Helper\Permission $webposPermission,
        \Magestore\Webpos\Model\Staff\StaffFactory $staff,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        SearchResultFactory $searchResultFactory,
        \Magestore\Webpos\Model\Pos\PosFactory $posFactory,
        \Magestore\Webpos\Model\Location\LocationFactory $locationFactory
    )
    {
        $this->_session = $session;
        $this->_timezone = $timezone;
        $this->_webPosSession = $webPosSession;
        $this->_staffFactory = $staff;
        $this->_permissionHelper = $webposPermission;
        $this->_request = $request;
        $this->_sessionManager = $sessionManager;
        $this->_searchResultFactory = $searchResultFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->posFactory = $posFactory;
        $this->locationFactory = $locationFactory;
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Staff\StaffInterface $staff
     * @return string
     */
    public function login($staff)
    {
        $username = $staff->getUsername();
        $password = $staff->getPassword();
        if ($username && $password) {
            try {
                $resultLogin = $this->_permissionHelper->login($username, $password);
                if ($resultLogin != 0) {
                    $this->_checkoutSession->setData('location_id', null);
                    $this->_checkoutSession->setData('pos_id', null);
                    $staffModel = $this->_staffFactory->create()->load($resultLogin);
                    $locationIds = $staffModel->getLocationId();
                    $locationIdsArray = explode(',', $locationIds);
                    $data = array();
                    if (count($locationIdsArray) == 1) {
//                        $data['location_id'] = $locationIdsArray[0];
//                        $data['store_view_id'] = $this->getStoreViewId($data['location_id']);
                    }
                    $data['staff_id'] = $resultLogin;
                    $this->_sessionManager->regenerateId();
                    $data['session_id'] = $this->_sessionManager->getSessionId();
                    $data['logged_date'] = strftime('%Y-%m-%d %H:%M:%S', $this->_timezone->scopeTimeStamp());
                    $this->_webPosSession->setData($data);
                    $this->_webPosSession->save();
//                    return json_encode($data);
                    return $data['session_id'];
                } else {
                    throw new StateException(__('Your account is invalid, Please try again'));
                }

            } catch (\Exception $e) {
                throw new StateException(__($e->getMessage()));
            }
        }
        throw new StateException(__('Your account is invalid, Please try again'));
    }

    protected function getStoreViewId($locationId) {
        /** @var \Magestore\Webpos\Model\Location\Location $model */
        $model = $this->locationFactory->create()->load($locationId);
        if(!$model->getId()) {
            throw new \Exception(__('Location does not exist!'));
        }
        $listStoreView = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magestore\Webpos\Helper\Data')
            ->getStoreView();
        $storeViewId = $listStoreView[0]['id']; // default store view id
        if($model->getData('store_id')) {
            $storeViewId = $model->getData('store_id');
        }
        return $storeViewId;
    }

    /**
     *
     * @return string
     */
    public function logout()
    {
        $sessionId = $this->_request->getParam('session');
        $sessionLoginCollection = $this->_webPosSession->getCollection()
            ->addFieldToFilter('session_id', $sessionId);
        foreach ($sessionLoginCollection as $sessionLogin) {
            $posId = $sessionLogin->getData('pos_id');
            if ($posId) {
                $posModel = $this->posFactory->create()->load($posId);
                $posModel->setData('staff_id', null);
                $posModel->save();
            }
            $sessionLogin->delete();
        }
        return true;
    }

    /**
     * @return boolean
     */
    public function forceLogout() {
        $sessionId = $this->_request->getParam('session');
        $curSessionModel = $this->_webPosSession->load($sessionId, 'session_id');
        $curStaffId = $curSessionModel->getData('staff_id');
        $curSessionModel->setData('is_allow_multi_pos', 1)->save();

        // delete exist session
        $sessionCollection = $this->_webPosSession->getCollection()
            ->addFieldToFilter('session_id', ['neq' => $sessionId])
            ->addFieldToFilter('staff_id', ['eq' => $curStaffId]);
        foreach ($sessionCollection as $session) {
            $session->delete();
        }

        // remove current staff from other pos
        $posCollection = $this->posFactory->create()->getCollection()
            ->addFieldToFilter('staff_id', ['eq' => $curStaffId]);
        foreach ($posCollection as $pos) {
            $pos->setStaffId('')->save();
        }

        return true;
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Staff\StaffInterface $staff
     * @return string
     */
    public function changepassword($staff)
    {
        $staffModel = $this->_staffFactory->create()->load($this->_permissionHelper->getCurrentUser());
        $result = [];
        if (!$staffModel->getId()) {
            $result['error'] = '401';
            $result['message'] = __('There is no staff!');
            return \Zend_Json::encode($result);
        }
        $staffModel->setDisplayName($staff->getUsername());
        $oldPassword = $staffModel->getPassword();
        if ($staffModel->validatePassword($staff->getOldPassword())) {
            if ($staff->getPassword()) {
                $staffModel->setPassword($staffModel->getEncodedPassword($staff->getPassword()));
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
                $sessionParam = $this->_request->getParam('session');
                $userSession = $this->_webPosSession->getCollection()
                    ->addFieldToFilter('staff_id', array('eq' => $staffModel->getId()))
                    ->addFieldToFilter('session_id', array('neq' => $sessionParam));
                foreach ($userSession as $session) {
                    $session->delete();
                }
            }
        } catch (\Exception $e) {
            $result['error'] = '1';
            $result['message'] = $e->getMessage();
            return \Zend_Json::encode($result);
        }
        $result['error'] = '0';
        $result['message'] = __('Your account is saved successfully!');
        return \Zend_Json::encode($result);
    }

    /**
     * get list Pos
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Staff\StaffSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Magestore\Webpos\Api\Data\Staff\StaffSearchResultsInterface $searchResult */
        $searchResult =  $this->_searchResultFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $this->addFilterGroupToCollection($filterGroup, $searchResult);
        }

        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders === null) {
            $sortOrders = [];
        }
        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            $searchResult->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }
        if($searchCriteria ->getCurrentPage()) {
            $searchResult->setCurPage($searchCriteria->getCurrentPage());
        }
        if($searchCriteria ->getPageSize()) {
            $searchResult->setPageSize($searchCriteria->getPageSize());
        }
        $searchResult->setSearchCriteria($searchCriteria);

        return $searchResult;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magestore\Webpos\Api\Data\Staff\StaffSearchResultsInterface $searchResult
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magestore\Webpos\Api\Data\Staff\StaffSearchResultsInterface $searchResult
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $conditions[] = [$condition => $filter->getValue()];
                $fields[] = $filter->getField();
        }
        if ($fields && count($fields) > 0) {
            $searchResult->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * get staff
     *
     * @return \Magestore\Webpos\Api\Data\Staff\StaffListDataInterface
     */
    public function get($staffId)
    {
        $staff = $this->_staffFactory->create()->load($staffId);
        if($staff->getId()) {
            return $staff;
        }
        return null;
    }

    /**
     * @param string $staffId
     * @param string $pin
     * @return \Magestore\Webpos\Api\Data\Staff\StaffListDataInterface $staff
     * @throws StateException
     */
    public function changeStaff($staffId, $pin)
    {
        $staffModel = $this->_staffFactory->create();
        $collection = $staffModel->getCollection()
                       ->addFieldToFilter('staff_id', $staffId)
                       ->addFieldToFilter('pin', $pin)
                        ;
        if($collection->getSize()) {
            $staff = $collection->getFirstItem();
            return $staff;
        }else {
            throw new StateException(__('PIN code is invalid'));
        }
    }
}