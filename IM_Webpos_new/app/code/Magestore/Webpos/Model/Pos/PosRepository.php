<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Pos;

use Magento\Framework\Exception\StateException;
use Magestore\Webpos\Api\Data\Pos\PosSearchResultsInterfaceFactory as SearchResultFactory;
use Magento\Framework\Api\SortOrder;

/**
 * Class PosRepository
 * @package Magestore\Webpos\Model\Pos
 */
class PosRepository implements \Magestore\Webpos\Api\Pos\PosRepositoryInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magestore\Webpos\Model\Pos\PosFactory
     */
    protected $posFactory;

    /**
     * @var  \Magestore\Webpos\Model\ResourceModel\Pos\Pos
     */
    protected $posResourceModel;

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Pos\Pos\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Staff\Staff\CollectionFactory
     */
    protected $staffCollectionFactory;

    /**
     * @var  \Magestore\Webpos\Helper\Permission
     */
    protected $permissionHelper;

    /**
     * @var  SearchResultFactory
     */
    protected $searchResultFactory;

    /**
     * @var \Magestore\Webpos\Model\Shift\ShiftFactory
     */
    protected $shiftFactory;

    /**
     * @var \Magestore\Webpos\Model\Staff\WebPosSessionFactory
     */
    protected $webPosSessionFactory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_request;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManager;
    /**
     * @var \Magestore\Webpos\Model\Staff\StaffFactory
     */
    protected $staffFactory;

    /**
     * PosRepository constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magestore\Webpos\Model\ResourceModel\Pos\Pos\CollectionFactory $collectionFactory
     * @param \Magestore\Webpos\Model\Pos\PosFactory $posFactory
     * @param \Magestore\Webpos\Model\ResourceModel\Pos\Pos $posResourceModel
     * @param \Magestore\Webpos\Helper\Permission $permissionHelper
     * @param \Magestore\Webpos\Model\ResourceModel\Staff\Staff\CollectionFactory $staffCollectionFactory
     * @param SearchResultFactory $searchResultFactory
     * @param \Magestore\Webpos\Model\Shift\ShiftFactory $shiftFactory
     * @param \Magestore\Webpos\Model\Staff\WebPosSessionFactory $webPosSessionFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Webpos\Model\Pos\PosFactory $posFactory,
        \Magestore\Webpos\Model\ResourceModel\Pos\Pos\CollectionFactory $collectionFactory,
        \Magestore\Webpos\Model\ResourceModel\Pos\Pos $posResourceModel,
        \Magestore\Webpos\Helper\Permission $permissionHelper,
        \Magestore\Webpos\Model\ResourceModel\Staff\Staff\CollectionFactory $staffCollectionFactory,
        SearchResultFactory $searchResultFactory,
        \Magestore\Webpos\Model\Shift\ShiftFactory $shiftFactory,
        \Magestore\Webpos\Model\Staff\WebPosSessionFactory $webPosSessionFactory,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\App\Request\Http $request,
        \Magestore\Webpos\Model\Staff\StaffFactory $staffFactory
    )
    {
        $this->storeManager = $storeManager;
        $this->collectionFactory = $collectionFactory;
        $this->posFactory = $posFactory;
        $this->posResourceModel = $posResourceModel;
        $this->permissionHelper = $permissionHelper;
        $this->staffCollectionFactory = $staffCollectionFactory;
        $this->searchResultFactory = $searchResultFactory;
        $this->shiftFactory = $shiftFactory;
        $this->webPosSessionFactory = $webPosSessionFactory;
        $this->_cookieManager = $cookieManager;
        $this->_request = $request;
        $this->staffFactory = $staffFactory;
    }

    /**
     * get list Pos
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Pos\PosSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria)
    {
        $helper = \Magento\Framework\App\ObjectManager::getInstance()->get(
            '\Magestore\Webpos\Helper\Data'
        );
        $isEnableSession = $helper->getStoreConfig('webpos/general/enable_session');
        if($isEnableSession) {
            /** @var \Magestore\Webpos\Api\Data\Pos\PosSearchResultsInterface $searchResult */
            $searchResult = $this->searchResultFactory->create();
            foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
                $this->addFilterGroupToCollection($filterGroup, $searchResult);
            }
            $searchResult->joinToLocation();

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
            if ($searchCriteria->getCurrentPage()) {
                $searchResult->setCurPage($searchCriteria->getCurrentPage());
            }
            if ($searchCriteria->getPageSize()) {
                $searchResult->setPageSize($searchCriteria->getPageSize());
            }
            $searchResult->setSearchCriteria($searchCriteria);
        } else {
//            var_dump($this->getLocationList($searchCriteria)->getSelect()->__toString());die('gg');
            return $this->getLocationList($searchCriteria);
        }
        return $searchResult;
    }

    /**
     * get list Pos
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Pos\PosSearchResultsInterface
     */
    public function getLocationList(\Magento\Framework\Api\SearchCriteria $searchCriteria)
    {
        /** @var \Magestore\Webpos\Api\Data\Pos\PosSearchResultsInterface $searchResult */
        $searchResult = $this->searchResultFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $this->addFilterLocationGroupToCollection($filterGroup, $searchResult);
        }
        $searchResult->joinRightToLocation();
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders === null) {
            $sortOrders = [];
        }
        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        foreach ($sortOrders as $sortOrder) {
            $field = $sortOrder->getField();
            if ($field == 'till_name' || $field == 'pos_name') {
                $field = 'location.display_name';
            }
            $searchResult->addOrder(
                $field,
                ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
            );
        }
        if ($searchCriteria->getCurrentPage()) {
            $searchResult->setCurPage($searchCriteria->getCurrentPage());
        }
        if ($searchCriteria->getPageSize()) {
            $searchResult->setPageSize($searchCriteria->getPageSize());
        }
        $searchResult->setSearchCriteria($searchCriteria);
        return $searchResult;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magestore\Webpos\Api\Data\Pos\PosSearchResultsInterface $searchResult
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magestore\Webpos\Api\Data\Pos\PosSearchResultsInterface $searchResult
    )
    {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $conditions[] = [$condition => $filter->getValue()];
            if($filter->getField() == 'location_id') {
                $filter->setField('main_table.location_id');
            }
            if ($filter->getField() == 'staff_id') {
                $searchResult->getAvailablePos($filter->getValue());
            } else {
                $fields[] = $filter->getField();
            }
        }
        if ($fields && count($fields) > 0) {
            $searchResult->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magestore\Webpos\Api\Data\Pos\PosSearchResultsInterface $searchResult
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterLocationGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magestore\Webpos\Api\Data\Pos\PosSearchResultsInterface $searchResult
    )
    {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $conditions[] = [$condition => $filter->getValue()];
//            if($filter->getField() == 'location_id') {
//                $filter->setField('location.location_id');
//            }
            if ($filter->getField() == 'staff_id') {
                $searchResult = $searchResult->getAvailableLocation($filter->getValue());
            } else {
                $fields[] = $filter->getField();
            }
        }
        if ($fields && count($fields) > 0) {
            $searchResult->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * get Pos
     *
     * @return \Magestore\Webpos\Api\Data\Pos\PosInterface
     */
    public function get($posId)
    {
        $pos = $this->posFactory->create()->load($posId);
        if ($pos->getId()) {
            return $pos;
        }
        return null;
    }

    /**
     * save Pos
     *
     * @param \Magestore\Webpos\Api\Data\Pos\PosInterface $pos
     * @return \Magestore\Webpos\Api\Data\Pos\PosInterface
     * @throws \Exception
     */
    public function save(\Magestore\Webpos\Api\Data\Pos\PosInterface $pos)
    {
        try {
            $this->posResourceModel->save($pos);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__('Unable to save pos'));
        }
        return $pos;
    }

    /**
     * assign staff for pos
     *
     * @param string $posId
     * @param string $locationId
     * @param string $currentSessionId
     * @return boolean
     * @throws StateException
     */
    public function assignStaff($posId, $locationId, $currentSessionId)
    {
        if(!$currentSessionId) {
            $currentSessionId = $this->_request->getParam('session');
        }
        if(!$currentSessionId) {
            $currentSessionId = $this->_cookieManager->getCookie('WEBPOSSESSION');
        }
        $sessionModel = $this->webPosSessionFactory->create()->load($currentSessionId, 'session_id');
        $result = true;
        if ($sessionModel->getId()) {
            try {
                $storeId = $sessionModel->getCurrentStoreId();
                $location = \Magento\Framework\App\ObjectManager::getInstance()->create(
                    '\Magestore\Webpos\Model\Location\LocationFactory'
                )->create()->load($locationId);
                if($location->getStoreId()) {
                    $storeId = $location->getStoreId();
                }
                if(!$storeId) {
                    $storeId = $this->_request->getParam('current_store_id');
                }
                $sessionModel->setData('current_store_id', $storeId);
                $sessionModel->setData('location_id', $locationId);
                $sessionModel->setData('pos_id', $posId);
                $sessionModel->save();
                $currentUserId = $sessionModel->getStaffId();
                $posModel = $this->posFactory->create()->load($posId);
                if ($posModel->getId()) {
                    $posModel->setData('staff_id', $currentUserId);
                    $posModel->save();
                }
                $openPos = $this->findOpenPos($posId);
                if ($openPos) {
                    $shiftModel = $this->shiftFactory->create()->load($openPos);
                    $shiftModel->setStaffId($currentUserId);
                    try {
                        $shiftModel->save();
                    } catch (\Exception $e) {
                        $result = false;
                    }
                }
            } catch (\Exception $e) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * unassign staff in pos
     * @param string $posId
     *
     * @return mixed
     */
    public function unassignStaff($posId)
    {
        $pos = $this->posFactory->create()->load($posId);
        $pos->setStaffId(null);
        try {
            $pos->save();
        } catch (\Exception $e) {

        }
    }

    /**
     * auto join all staff for pos
     * @param string $posId
     *
     * @return mixed
     */
    public function autoJoinAllStaffs($posId)
    {
        $staffCollection = $this->staffCollectionFactory->create();
        foreach ($staffCollection as $staff) {
            $posIds = $staff->getPosIds();
            $posIds = explode(',', $posIds);
            $posIds[] = $posId;
            $posIds = array_unique($posIds);
            $posIds = implode(',', $posIds);
            $staff->setPosIds($posIds);
            try {
                $staff->save();
            } catch (\Exception $e) {

            }
        }
    }

    /**
     * @param $posId
     * @return mixed
     */
    public function findOpenPos($posId)
    {
        $openPos = $this->shiftFactory->create()->getCollection()
            ->addFieldToFilter('pos_id', $posId)
            ->addFieldToFilter('status', 0)
            ->getFirstItem();
        return $openPos->getEntityId();
    }

    /**
     * Change pin
     *
     * @param \Magestore\Webpos\Api\Data\Register\PinInterface $pin
     * @return boolean
     */
    public function changePin(\Magestore\Webpos\Api\Data\Register\PinInterface $pin)
    {
        $staffId = $pin->getStaffId();
        $pinCode = $pin->getPinCode();
        $password = $pin->getPassword();
        $posId = $pin->getPosId();
        $staffModel = $this->staffFactory->create()->load($staffId);

        if ($staffModel->getId()){
            $staffUserName = $staffModel->getData('username');
            $posList = $staffModel->getData('pos_ids');
            $posListArray = explode(',', $posList);
            $isAuthorize = $staffModel->authenticate($staffUserName, $password);
            if ($isAuthorize && in_array($posId, $posListArray)){
                $posModel = $this->posFactory->create();
                $posModel->load($posId);
                if ($posModel->getId()){
                    $posModel->setData('pin', $pinCode);
                    $posModel->save();
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * Unlock Pos
     * @param string $pin
     * @param string $posId
     * @return boolean
     */
    public function unlockPos($pin, $posId)
    {
        try {
            $posModel = $this->posFactory->create();
            $posModel->load($posId);
            $posCode = $posModel->getPin();
            if (!$this->permissionHelper->isAllowResource('Magestore_Webpos::lock_unlock_register')) {
                throw new \Exception(__('Permission denied. Please contact Administrator to unlock the register.'));
            }
            if ($pin == $posCode) {
                $this->unsetStaffLockedForPos($posModel);
                $posModel->setStatus(\Magestore\Webpos\Model\Pos\Status::STATUS_ENABLED);
                $posModel->save();
                $response = ['success' => true];
            } else {
                $response = ['success' => false, 'message' => __('Invalid security PIN. Please try again!')];
            }
        }  catch (\Exception $ex) {
            $response = ['success' => false, 'message' => $ex->getMessage()];
        }
        return \Zend_Json::encode($response);
    }

    public function unsetStaffLockedForPos($posModel) {
        $posModel->setStaffLocked(NULL);
    }

    /**
     * Check Pos
     * @param string $posId
     * @return boolean
     */
    public function checkPos($posId)
    {
        $posModel = $this->posFactory->create();
        $posModel->load($posId);
        $staffLocked = $posModel->getStatus();
        if ($staffLocked == \Magestore\Webpos\Model\Pos\Status::STATUS_LOCKED) {
            return true;
        } else {
            return false;
        }
    }
}