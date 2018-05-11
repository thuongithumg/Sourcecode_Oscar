<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created by PhpStorm.
 * User: steve
 * Date: 06/06/2016
 * Time: 13:42
 */

namespace Magestore\Webpos\Model\Shift;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magestore\Webpos\Api\Data\Shift\ShiftInterface;
use Magestore\Webpos\Api\Data\Shift\ShiftSearchResultsInterfaceFactory as SearchResultFactory;
use Magento\Framework\Api\SortOrder;


class ShiftRepository implements \Magestore\Webpos\Api\Shift\ShiftRepositoryInterface
{
    /*
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /** @var $shiftFactory  \Magestore\Webpos\Model\Shift\ShiftFactory */

    protected $_shiftFactory;

    /** @var  $transactionFactory \Magestore\Webpos\Model\Shift\TransactionFactory */
    protected $_cashTransactionFactory;

    /** @var \Magestore\Webpos\Model\ResourceModel\Shift\Shift\CollectionFactory */

    protected $_shiftCollectionFactory;

    /**
     * @var \Magento\Framework\Api\ExtensibleDataObjectConverter
     */
    protected $_extensibleDataObjectConverter;

    /** @var  \Magestore\Webpos\Helper\Permission */
    protected $_permissionHelper;

    /** @var  \Magestore\Webpos\Helper\Shift */
    protected $_shiftHelper;

    /** @var  \Magestore\Webpos\Model\Pos\PosRepository */
    protected $posRepository;

    /** @var  SearchResultFactory */
    protected $searchResultFactory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * ShiftRepository constructor.
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param \Magestore\Webpos\Model\ResourceModel\Shift\Shift $shiftResource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param ShiftFactory $shiftFactory
     * @param CashTransactionFactory $cashTransactionFactory
     * @param \Magestore\Webpos\Helper\Permission $permissionHelper
     * @param \Magestore\Webpos\Helper\Shift $shiftHelper
     * @param \Magestore\Webpos\Model\Pos\PosRepository $posRepository
     * @param SearchResultFactory $searchResultFactory
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \Magestore\Webpos\Model\ResourceModel\Shift\Shift $shiftResource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Webpos\Model\Shift\ShiftFactory $shiftFactory,
        \Magestore\Webpos\Model\Shift\CashTransactionFactory $cashTransactionFactory,
        \Magestore\Webpos\Helper\Permission $permissionHelper,
        \Magestore\Webpos\Helper\Shift $shiftHelper,
        \Magestore\Webpos\Model\Pos\PosRepository $posRepository,
        \Magestore\Webpos\Model\ResourceModel\Shift\Shift\CollectionFactory $shiftCollectionFactory,
        SearchResultFactory $searchResultFactory,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->_extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->shiftResource = $shiftResource;
        $this->_storeManager = $storeManager;
        $this->_shiftFactory = $shiftFactory;
        $this->_cashTransactionFactory = $cashTransactionFactory;
        $this->_permissionHelper = $permissionHelper;
        $this->_shiftHelper = $shiftHelper;
        $this->posRepository = $posRepository;
        $this->searchResultFactory = $searchResultFactory;
        $this->_shiftCollectionFactory = $shiftCollectionFactory;
        $this->request = $request;
    }

    /**
     * get a list of Shift for a specific staff_id.
     * Because in the frontend we just need to show all shift for "this week"
     * so we will return this week shift only.
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Magestore\Webpos\Api\Data\Shift\ShiftSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria)
    {
        /** @var \Magestore\Webpos\Api\Data\Shift\ShiftSearchResultsInterface $searchResult */
        $searchResult = $this->searchResultFactory->create();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $this->addFilterGroupToCollection($filterGroup, $searchResult);
        }
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $objectManager->get(
            'Magento\Catalog\Model\ResourceModel\Product\Collection'
        );
        /** @var \Magento\Framework\DB\Select $select */
        $select = $searchResult->getSelect();
        $select->joinLeft(
            ['staff' => $collection->getTable('webpos_staff')],
            'main_table.open_by = staff.staff_id',
            ['opener'=>'staff.display_name']
        );

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
        $this->addShiftTimeRangeFilter($searchResult);
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
     * create datetime range from, to
     * @return array
     */
    public function addShiftTimeRangeFilter($searchResult)
    {
        $days = $this->_shiftHelper->getStoreConfig('webpos/general/day_to_show_session_history');
        if($days > 0) {
            //filter just last 7 days shift by created_at
            $time = time();
            $to = date('Y-m-d H:i:s', $time);
            $lastTime = $time - 60 * 60 * 24 * $days;
            $from = date('Y-m-d H:i:s', $lastTime);
            $searchResult->getSelect()->where("('opened_at' >= '$from' AND 'opened_at' <= '$to') OR 'status' = 0 ");
        } else if($days == -1){
            $searchResult->addFieldToFilter('status',0);
        }
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Magestore\Webpos\Api\Data\Shift\ShiftSearchResultsInterface $searchResult
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Magestore\Webpos\Api\Data\Shift\ShiftSearchResultsInterface $searchResult
    )
    {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $conditions[] = [$condition => $filter->getValue()];
            $fields[] = $filter->getField();
        }
        if ($fields) {
            $searchResult->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * get detail information of a shift with specify shift_id
     * this function call to detail function of Shift Model
     * @param int $shift_id
     * @return mixed
     */
    public function detail($shift_id)
    {
        $shiftModel = $this->_shiftFactory->create();
        $data = $shiftModel->detail($shift_id);

        return $data;
    }

    /**
     * save a shift with its data in $shift (ShiftInterface)
     * @param \Magestore\Webpos\Api\Data\Shift\ShiftInterface $shift
     * @return mixed
     * @throws StateException
     * @throws CouldNotSaveException
     */
    public function save(ShiftInterface $shift)
    {
        //$indexeddbId = $shift->getIndexeddbId();
        $shiftId = $shift->getShiftId();
        $shiftModel = $this->_shiftFactory->create();

        if (!$shiftId) {
            return;
        } else {
//            $this->checkAssignPosStaff($shift);
            $shiftModel->load($shiftId, "shift_id");
        }

        if ($shiftModel->getShiftId()) {
            $shift->setEntityId($shiftModel->getEntityId());
        } else {
            $shift->setEntityId(null);
            $checkOpenedShift = $this->checkOpenedShift();
            if ($checkOpenedShift) {
                throw new StateException(__('Please close your session before opening a new one'));
            }
        }

        // Do not add real amount to cash remove
//        if ($shift->getStatus() == 1) {
//
//            $balance = $shift->getData("base_closed_amount") - $shift->getData("base_cash_left");
//            if ($balance > 0) {
//                //create removed cash transaction
//                $cashTransactionData = [
//                    "shift_id" => $shift->getData("shift_id"),
//                    "location_id" => $shift->getData("location_id"),
//                    "value" => $shift->getData("closed_amount") - $shift->getData("cash_left"),
//                    "base_value" => $shift->getData("base_closed_amount") - $shift->getData("base_cash_left"),
//                    "note" => "Remove cash when closed Session",
//                    "balance" => $shift->getData("balance"),
//                    "base_balance" => $shift->getData("base_balance"),
//                    "type" => "remove",
//                    "base_currency_code" => $shift->getData("base_currency_code"),
//                    "transaction_currency_code" => $shift->getData("shift_currency_code"),
//                ];
//                $transactionModel = $this->_cashTransactionFactory->create();
//                $transactionModel->setData($cashTransactionData);
//
//                try {
//                    $transactionModel->save();
//                } catch (\Exception $exception) {
//
//                    throw new CouldNotSaveException(__($exception->getMessage()));
//                }
//            }
//        }

        try {
            $this->shiftResource->save($shift);
        } catch (\Exception $exception) {


            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        $shiftData = $this->_shiftHelper->prepareOfflineShiftData($shift->getShiftId());
        $zReportSalesSummary = $shiftData["zreport_sales_summary"];
        $shiftData["zreport_sales_summary"] = $zReportSalesSummary->getData();
        $response[] = $shiftData;

        return $response;
    }

    /**
     * get shit information
     *
     * @return array
     */
    public function get($shiftId)
    {
        $shiftModel = $this->_shiftFactory->create()->load($shiftId);
        return $shiftModel;
    }

    /**
     * get shit information
     *
     * @return array
     */
    public function getInfo($shiftId)
    {
        $shift = $this->get($shiftId);
        $data = $shift->getInfo();
        return $data;

    }

    /**
     * check assign pos & staff
     *
     * @param \Magestore\Webpos\Api\Data\Shift\ShiftInterface $shift
     * @return mixed
     */
    public function checkAssignPosStaff($shift)
    {
        $posId = $shift->getPosId();
        $staffId = $shift->getStaffId();
        $status = $shift->getStatus();
        if ($posId && $staffId) {
            if ($status == 1) {
                $this->posRepository->unassignStaff($staffId);
            } else if ($status == 0) {
                $currentSessionId = $this->request->getParam('session');
                $locationId = $shift->getLocationId();
                $this->posRepository->assignStaff($posId, $locationId, $currentSessionId);
            }
        }
    }

    /**
     * check opened session/shift by staff id
     *
     * @params string $staffId
     * @return boolean
     */
    public function checkOpenedShift($staffId = null)
    {
        $collection = $this->_shiftCollectionFactory->create();
        if ($staffId) {
            $collection->addFieldToFilter('staff_id', $staffId)
                ->addFieldToFilter('pos_id', $this->_permissionHelper->getCurrentPosId())
                ->addFieldToFilter('status', 0);
        } else {
            $collection->addFieldToFilter('pos_id', $this->_permissionHelper->getCurrentPosId())
                ->addFieldToFilter('status', 0);
        }

        if ($collection->getSize() > 0) {
            return true;
        }
        return false;
    }

    /**
     * get open session for pos
     *
     * @param string $posId
     * @return \Magestore\Webpos\Api\Data\Shift\ShiftInterface[]
     */
    public function getOpenSession($posId = '')
    {
        $collection = $this->_shiftCollectionFactory->create();
        if ($posId) {
            $collection->addFieldToFilter('pos_id', $posId);
        }
        $collection->addFieldToFilter('status', 0);
        if ($collection->getSize() > 0) {
            return $collection->getItems();
        }
        return [];
    }
}
