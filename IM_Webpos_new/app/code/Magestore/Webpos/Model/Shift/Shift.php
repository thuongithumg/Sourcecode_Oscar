<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created by PhpStorm.
 * User: steve
 * Date: 06/06/2016
 * Time: 13:29
 */

namespace Magestore\Webpos\Model\Shift;

use \Magento\Framework\Model\AbstractExtensibleModel as AbstractExtensibleModel;
use \Magestore\Webpos\Api\Data\Shift\ShiftInterface as ShiftInterface;
use Magento\Framework\Exception\CouldNotSaveException;


/**
 * Class Shift
 * @package Magestore\Webpos\Model\Shift
 */

class Shift extends AbstractExtensibleModel implements ShiftInterface
{

    /** @var $_saleSummaryFactory  \Magestore\Webpos\Model\Shift\SaleSummaryFactory */
    protected $_saleSummaryFactory;

    /** @var  $transactionFactory \Magestore\Webpos\Model\Shift\TransactionFactory */
    protected $_cashTransactionFactory;

    /** @var  $staffFactory \Magestore\Webpos\Model\Staff\StaffFactory $staffFactory */
    protected $_staffFactory;

    /** @var  \Magestore\Webpos\Helper\Currency */
    protected $_webposCurrencyHelper;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;


    /**
     * Shift constructor.
     * @param SaleSummaryFactory $saleSummaryFactory
     * @param CashTransactionFactory $cashTransactionFactory
     * @param \Magestore\Webpos\Model\Staff\StaffFactory $staffFactory
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magestore\Webpos\Helper\Currency $webposCurrencyHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magestore\Webpos\Model\Shift\SaleSummaryFactory $saleSummaryFactory,
        \Magestore\Webpos\Model\Shift\CashTransactionFactory $cashTransactionFactory,
        \Magestore\Webpos\Model\Staff\StaffFactory $staffFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magestore\Webpos\Helper\Currency $webposCurrencyHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->_eventManager = $context->getEventDispatcher();
        $this->_saleSummaryFactory = $saleSummaryFactory;
        $this->_cashTransactionFactory = $cashTransactionFactory;
        $this->_staffFactory = $staffFactory;
        $this->_storeManager = $storeManager;
        $this->_webposCurrencyHelper = $webposCurrencyHelper;
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $resource, $resourceCollection, $data);
    }


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\ResourceModel\Shift\Shift');
    }

    /**
     * Processing object before save data
     *
     * @return $this
     */
    public function beforeSave()
    {
        if (!$this->getId()){
            $this->setData('open_by',$this->getStaffId());
        }
        return parent::beforeSave();
    }

    /**
     *  indexeddb_id
     * @return string
     */
    public function getIndexeddbId(){
        return $this->getData(self::INDEXEDDB_ID);
    }


    /**
     * Set indexeddb_id
     *
     * @param string $indexeddb_id
     * @return $this
     */
    public function setIndexeddbId($indexeddbId){
        return $this->setData(self::INDEXEDDB_ID, $indexeddbId);
    }

    /**
     *  Entity id
     * @return int
     */
    public function getEntityId()
    {
        return (int)$this->getData(self::ENTITY_ID);
    }

    /**
     * Set Entity Id
     *
     * @param int $entity_id
     * @return $this
     */
    public function setEntityId($entity_id)
    {
        return $this->setData(self::ENTITY_ID, $entity_id);
    }

    /**
     *  shift id
     * @return string
     */
    public function getShiftId()
    {
        return $this->getData(self::SHIFT_ID);
    }

    /**
     * Set Shift Id
     *
     * @param string $shiftId
     * @return $this
     */
    public function setShiftId($shiftId)
    {
        return $this->setData(self::SHIFT_ID, $shiftId);
    }

    /**
     *  staff id
     * @return int|null
     */
    public function getStaffId()
    {
        return $this->getData(self::STAFF_ID);
    }

    /**
     * Set Staff Id
     *
     * @param int $staffId
     * @return $this
     */
    public function setStaffId($staffId)
    {
        return $this->setData(self::STAFF_ID, $staffId);
    }

    /**
     *  location id
     * @return int|null
     */
    public function getLocationId()
    {
        return $this->getData(self::LOCATION_ID);
    }

    /**
     * Set Location_Id
     *
     * @param int $locationId
     * @return $this
     */
    public function setLocationId($locationId)
    {
        return $this->setData(self::LOCATION_ID, $locationId);
    }

    /**
     *  open time
     * @return datetime/null
     */
    public function getOpenedAt()
    {
        return $this->getData(self::OPENED_AT);
    }

    /**
     * set Opended At
     * @param datetime $openedAt
     * @return $this
     */
    public function setOpenedAt($openedAt){
        return $this->setData(self::OPENED_AT, $openedAt);
    }

    /**
     *  get updated at
     * @return string|null updated_at
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     *  set updated at
     * @return string|null updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        return $this->setData(self::UPDATED_AT, $updated_at);
    }
    
    
    /**
     *  closed at
     * @return datetime
     */
    public function getClosedAt()
    {
        return $this->getData(self::CLOSED_AT);
    }

    /**
     * set Closed At
     * @param datetime $closed_at
     * @return $this
     */
    public function setClosedAt($closedAt)
    {
        return $this->setData(self::CLOSED_AT, $closedAt);
    }

    /**
     * float amount when open shift
     * @return float
     */
    public function getFloatAmount()
    {
        return $this->getData(self::FLOAT_AMOUNT);
    }

    /**
     * Set Float Amount
     * @param float @float_amount
     * @return $this
     */
    public function setFloatAmount($floatAmount)
    {
        return $this->setData(self::FLOAT_AMOUNT, $floatAmount);
    }

    /**
     * get base float amount when open shift
     * @return float
     */
    public function getBaseFloatAmount()
    {
        return $this->getData(self::BASE_FLOAT_AMOUNT);
    }

    /**
     * Set Base Float Amount
     * @param float @base_float_amount
     * @return $this
     */
    public function setBaseFloatAmount($baseFloatAmount)
    {
        return $this->setData(self::BASE_FLOAT_AMOUNT, $baseFloatAmount);
    }

    /**
     * closed amount when open shift
     * @return float
     */
    public function getClosedAmount()
    {
        return $this->getData(self::CLOSED_AMOUNT);
    }

    /**
     * set Closed Amount
     * @param float @closed_amount
     * @return $this
     */
    public function setClosedAmount($closedAmount)
    {
        return $this->setData(self::CLOSED_AMOUNT, $closedAmount);
    }

    /**
     * base closed amount when open shift
     * @return float
     */
    public function getBaseClosedAmount()
    {
        return $this->getData(self::BASE_CLOSED_AMOUNT);
    }

    /**
     * set Base Closed Amount
     * @param float @base_closed_amount
     * @return $this
     */
    public function setBaseClosedAmount($baseClosedAmount)
    {
        return $this->setData(self::BASE_CLOSED_AMOUNT, $baseClosedAmount);
    }

    /**
     * status
     * @return int
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * set Status
     * @param int @status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * cash left in cash drawer when close shift
     * @return float
     */
    public function getCashLeft()
    {
        return $this->getData(self::CASH_LEFT);
    }

    /**
     * set Cash Left
     * @param float @cash_left
     * @return $this
     */
    public function setCashLeft($cashLeft)
    {
        return $this->setData(self::CASH_LEFT, $cashLeft);
    }

    /**
     * base cash left in cash drawer when close shift
     * @return float
     */
    public function getBaseCashLeft()
    {
        return $this->getData(self::BASE_CASH_LEFT);
    }

    /**
     * set Base Cash Left
     * @param float @base_cash_left
     * @return $this
     */
    public function setBaseCashLeft($baseCashLeft)
    {
        return $this->setData(self::BASE_CASH_LEFT, $baseCashLeft);
    }

    /**
     * get closed note
     * @return string
     */
    public function getClosedNote()
    {
        return $this->getData(self::CLOSED_NOTE);
    }

    /**
     * @param string $closedNote
     * @return $this
     */
    public function setClosedNote($closedNote)
    {
        return $this->setData(self::CLOSED_NOTE, $closedNote);
    }

    /**
     * get Opened note
     * @return string
     */
    public function getOpenedNote()
    {
        return $this->getData(self::OPENED_NOTE);
    }

    /**
     * @param string $openedNote
     * @return $this
     */
    public function setOpenedNote($openedNote)
    {
        return $this->setData(self::OPENED_NOTE, $openedNote);
    }

    /**
     * Total sales for a shift
     * @return float
     */
    public function getTotalSales()
    {
        return $this->getData(self::TOTAL_SALES);
    }

    /**
     * set total sales
     * @param float @totalSales
     * @return $this
     */
    public function setTotalSales($totalSales)
    {
        return $this->setData(self::TOTAL_SALES, $totalSales);
    }

    /**
     * Base Total sales for a shift
     * @return float
     */
    public function getBaseTotalSales()
    {
        return $this->getData(self::BASE_TOTAL_SALES);
    }

    /**
     * set base total sales
     * @param float @baseTotalSales
     * @return $this
     */
    public function setBaseTotalSales($baseTotalSales)
    {
        return $this->setData(self::BASE_TOTAL_SALES, $baseTotalSales);
    }


    /**
     * get balance
     * @return float
     */
    public function getBalance()
    {
        return $this->getData(self::BALANCE);
    }

    /**
     * set balance
     * @param float @balance
     * @return $this
     */
    public function setBalance($balance)
    {
        return $this->setData(self::BALANCE, $balance);
    }

    /**
     * get base_balance
     * @return float
     */
    public function getBaseBalance()
    {
        return $this->getData(self::BASE_BALANCE);
    }

    /**
     * set baseBalance
     * @param float @baseBalance
     * @return $this
     */
    public function setBaseBalance($baseBalance)
    {
        return $this->setData(self::BASE_BALANCE, $baseBalance);
    }

    /**
     * get cash_sale
     * @return float
     */
    public function getCashSale() {
        return $this->getData(self::CASH_SALE);
    }


    /**
     * set cashSale
     * @param float @cashSale
     * @return $this
     */
    public function setCashSale($cashSale) {
        return $this->setData(self::CASH_SALE, $cashSale);
    }

    /**
     * get base_cash_sale
     * @return float
     */
    public function getBaseCashSale() {
        return $this->getData(self::BASE_CASH_SALE);
    }


    /**
     * set baseCashSale
     * @param float @baseCashSale
     * @return $this
     */
    public function setBaseCashSale($baseCashSale) {
        return $this->setData(self::BASE_CASH_SALE, $baseCashSale);
    }

    /**
     * get cash_added
     * @return float
     */
    public function getCashAdded() {
        return $this->getData(self::CASH_ADDED);
    }

    /**
     * set cashAdded
     * @param float @cashAdded
     * @return $this
     */
    public function setCashAdded($cashAdded) {
        return $this->setData(self::CASH_ADDED, $cashAdded);
    }

    /**
     * set base_cash_added
     * @param float @base_cash_added
     * @return $this
     */
    public function setBaseCashAdded($baseCashAdded) {
        return $this->setData(self::BASE_CASH_ADDED, $baseCashAdded);
    }

    /**
     * get base_cash_added
     * @return float
     */
    public function getBaseCashAdded() {
        return $this->getData(self::BASE_CASH_ADDED);
    }

    /**
     * get cash_removed
     * @return float
     */
    public function getCashRemoved() {
        return $this->getData(self::CASH_REMOVED);
    }


    /**
     * set cash_removed
     * @param float @cash_removed
     * @return $this
     */
    public function setCashRemoved($cashRemoved) {
        return $this->setData(self::CASH_REMOVED, $cashRemoved);
    }

    /**
     * get base_cash_removed
     * @return float
     */
    public function getBaseCashRemoved() {
        return $this->getData(self::BASE_CASH_REMOVED);
    }

    /**
     * set baseCashRemoved
     * @param float @baseCashRemoved
     * @return $this
     */
    public function setBaseCashRemoved($baseCashRemoved) {
        return $this->setData(self::BASE_CASH_REMOVED, $baseCashRemoved);
    }

    /**
     * get base_cash_removed
     * @return float
     */
    public function getBaseCashRefunded() {
        return $this->getData(self::BASE_CASH_REFUNDED);
    }

    /**
     * set baseCashRemoved
     * @param float @baseCashRemoved
     * @return $this
     */
    public function setBaseCashRefunded($baseCashRefunded) {
        return $this->setData(self::BASE_CASH_REFUNDED, $baseCashRefunded);
    }

    /**
     * get base_cash_removed
     * @return float
     */
    public function getCashRefunded() {
        return $this->getData(self::CASH_REFUNDED);
    }

    /**
     * set baseCashRemoved
     * @param float @CashRefunded
     * @return $this
     */
    public function setCashRefunded($baseCashRefunded) {
        return $this->setData(self::CASH_REFUNDED, $baseCashRefunded);
    }

    /** get Session
     * @return mixed
     */
    public function getSession(){
        return $this->getData(self::SESSION);
    }

    /** set Session
     * @param $session
     * @return mixed
     */
    public function setSession($session){
        return $this->setData(self::SESSION, $session);
    }

    /**
     * get base currency code
     * @return string
     */
    public function getBaseCurrencyCode(){
        return $this->getData(self::BASE_CURRENCY_CODE);
    }

    /**
     * set base currency code
     * @param string $base_currency_code
     * @return $this
     */
    public function setBaseCurrencyCode($baseCurrencyCode){
        return $this->setData(self::BASE_CURRENCY_CODE,$baseCurrencyCode);
    }

    /**
     * get shift currency code
     * @return string
     */
    public function getShiftCurrencyCode(){
        return $this->getData(self::SHIFT_CURRENCY_CODE);
    }

    /**
     * set shift currency code
     * @param string $shiftCurrencyCode
     * @return $this
     */
    public function setShiftCurrencyCode($shiftCurrencyCode){
        return $this->setData(self::SHIFT_CURRENCY_CODE,$shiftCurrencyCode);
    }

    /**
     * set reason
     * @param string $reason
     * @return $this
     */
    public function setProfitLossReason($reason){
        return $this->setData(self::PROFIT_LOSS_REASON, $reason);
    }

    /**
     * get reason
     * @return string
     */
    public function getProfitLossReason(){
        return $this->getData(self::PROFIT_LOSS_REASON);
    }

    /**
     * get pos id
     * @return string
     */
    public function getPosId()
    {
        return $this->getData(self::POS_ID);
    }

    /**
     * get pos name
     * @return string
     */
    public function getPosName()
    {
        $posId = $this->getData(self::POS_ID);
        $posName = '';
        $posRepository = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\Webpos\Model\Pos\PosRepository');
        if($posId) {
            $pos = $posRepository->get($posId);
            if ($pos) {
                $posName = $pos->getPosName();
            }
        }
        return $posName;
    }

    /**
     * get staff name
     * @return string
     */
    public function getStaffName()
    {
        $staffId = $this->getData(self::STAFF_ID);
        $staffName = '';
        $staffRepository = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\Webpos\Model\Staff\StaffManagement');
        if($staffId) {
            $staff = $staffRepository->get($staffId);
            if ($staff) {
                $staffName = $staff->getData('display_name');
                $staffName = ($staffName) ? $staffName : $staff->getUsername();
            }
        }
        return $staffName;
    }

    /**
     * get staff name
     * @return string
     */
    public function getOpenerName()
    {
        $staffId = $this->getData('open_by');
        $staffName = '';
        $staffRepository = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\Webpos\Model\Staff\StaffManagement');
        if($staffId) {
            $staff = $staffRepository->get($staffId);
            $staffName = $staff->getData('display_name');
            $staffName = ($staffName)?$staffName:$staff->getUsername();
        }
        return $staffName;
    }

    /**
     * get opener name
     * @return string
     */
    public function getOpener()
    {
        return $this->getData('opener');
    }

    /**
     * get pos name
     * @return string
     */
    public function getStoreId()
    {
        $posId = $this->getData(self::POS_ID);
        $storeId = '';
        $posRepository = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Magestore\Webpos\Model\Pos\PosRepository');
        if($posId) {
            $pos = $posRepository->get($posId);
            $storeId = $pos->getStoreId();
        }
        return $storeId;
    }

    /**
     * set pos id
     * @param string $posId
     * @return $this
     */
    public function setPosId($posId)
    {
        return $this->setData(self::POS_ID, @$posId);
    }

    /**
     * get sale summary
     * @return array
     */
    public function getSaleSummary()
    {
        $saleSummaryModel = $this->_saleSummaryFactory->create();
        $saleSummaryData = $saleSummaryModel->getSaleSummary($this->getShiftId());
        return $saleSummaryData;
    }

    /**
     * get cash transaction
     * @return array
     */
    public function getCashTransaction()
    {
        $cashTransactionModel = $this->_cashTransactionFactory->create();
        $transactionData = $cashTransactionModel->getByShiftId($this->getShiftId());
        return $transactionData;
    }

    /**
     * get zreport sales summary
     * @return \Magestore\Webpos\Api\Data\Shift\SaleSummary
     */
    public function getZreportSalesSummary()
    {
        $saleSummaryModel = $this->_saleSummaryFactory->create();
        $zReportSalesSummary = $saleSummaryModel->getZReportSalesSummary($this->getShiftId());
        return $zReportSalesSummary;
    }

    /**
     * get a list of Shift for a specific staff_id.
     * Because in the frontend we just need to show all shift for "this week"
     * so we will return this week shift only.
     *
     * @param integer $staffId
     * @return array
     */
    public function getList($staffId)
    {
        /** @var \Magestore\Webpos\Model\Staff\Staff $staffModel */
        $staffModel = $this->_staffFactory->create()->load($staffId);

        /** @var \Magestore\Webpos\Model\Shift\SaleSummary $saleSummaryModel */
        $saleSummaryModel = $this->_saleSummaryFactory->create();

        /** @var \Magestore\Webpos\Model\Shift\CashTransaction $cashTransactionModel */
        $cashTransactionModel = $this->_cashTransactionFactory->create();

        /** @var  $shiftCollection \Magestore\Webpos\Model\ResourceModel\Shift\Shift\Collection */
        $shiftCollection = $this->getCollection();
        $shiftCollection->addFieldToFilter("staff_id", $staffId);
        $shiftCollection->addFieldToFilter('opened_at', $this->getShiftTimeRangeFilter());
        $shiftCollection->setOrder("shift_id", "DESC");
        //$select = $shiftCollection->getSelect();

        //return data
        $data = [];

        foreach ($shiftCollection as $item) {
            $itemData = $item->getData();
            $itemData['shift_id'] = $itemData['shift_id'];
            //Convert all shift data to currentCurrency and baseCurrency data
            $itemData = $this->updateShiftDataCurrency($itemData);
            $itemData['staff_name'] =  $staffModel->getDisplayName();
            //get all sale summary data of the shift with id=$itemData['shift_id']
            $saleSummaryData = $saleSummaryModel->getSaleSummary($itemData['shift_id']);
            //get all cash transaction data of the shift with id=$itemData['shift_id']
            $transactionData = $cashTransactionModel->getByShiftId($itemData['shift_id']);
            //get data for zreport
            $zReportSalesSummary = $saleSummaryModel->getZReportSalesSummary($itemData['shift_id']);
            $itemData["sale_summary"] = $saleSummaryData;
            $itemData["cash_transaction"] = $transactionData;
            $itemData["zreport_sales_summary"] = $zReportSalesSummary;
            $data[] = $itemData;
        }

        return $data;
    }

    /**
     * get shift
     * @param \Magestore\Webpos\Api\Data\Shift\ShiftInterface $shift
     * @return array
     */
    public function getInfo()
    {
        /** @var \Magestore\Webpos\Model\Shift\SaleSummary $saleSummaryModel */
        $saleSummaryModel = $this->_saleSummaryFactory->create();

        /** @var \Magestore\Webpos\Model\Shift\CashTransaction $cashTransactionModel */
        $cashTransactionModel = $this->_cashTransactionFactory->create();
        $data = $this->getData();
        //Convert all shift data to currentCurrency and baseCurrency data
        $data = $this->updateShiftDataCurrency($data);
//        $data['staff_name'] =  $staffModel->getDisplayName();
        //get all sale summary data of the shift with id=$itemData['shift_id']
        $saleSummaryData = $saleSummaryModel->getSaleSummary($data['shift_id']);
        //get all cash transaction data of the shift with id=$itemData['shift_id']
        $transactionData = $cashTransactionModel->getByShiftId($data['shift_id']);
        //get data for zreport
        $zReportSalesSummary = $saleSummaryModel->getZReportSalesSummary($data['shift_id']);
        $data["sale_summary"] = $saleSummaryData;
        $data["cash_transaction"] = $transactionData;
        $data["zreport_sales_summary"] = $zReportSalesSummary;
        $data["staff_name"] = $this->getStaffName();
        $data["opener"] = $this->getOpenerName();
        $data["pos_name"] = $this->getPosName();

        return $data;
    }

    /**check if shift_currency_code is change
     * and check if base_currency_code is change.
     * if yes, update new data for shift then update value for shift_currency_code and base_currency_code.
     *
     * @param $data
     */
    public function updateShiftDataCurrency($data){

        //currency code that stored in shift record
        $shiftBaseCurrencyCode = $data['base_currency_code'];
        $shiftCurrencyCode = $data['shift_currency_code'];
        //current currency code of the system now
        $baseCurrencyCode = $this->_webposCurrencyHelper->getBaseCurrencyCode();
        $currentCurrencyCode = $this->_webposCurrencyHelper->getCurrentCurrencyCode();
        //no thing change on currency, so we don't need to convert data.
        if(($shiftBaseCurrencyCode == $baseCurrencyCode) && ($shiftCurrencyCode == $currentCurrencyCode)){
            return $data;
        }
        //stored baseCurrencyCode is different from the base currency code now
        //convert all old base currency value to new base currency value
        if($shiftBaseCurrencyCode != $baseCurrencyCode){
            $data['base_currency_code'] = $baseCurrencyCode;
            $data['base_float_amount'] = $this->_webposCurrencyHelper->currencyConvert($data['base_float_amount'],$shiftBaseCurrencyCode, $baseCurrencyCode );
            $data['base_closed_amount'] = $this->_webposCurrencyHelper->currencyConvert($data['base_closed_amount'],$shiftBaseCurrencyCode, $baseCurrencyCode );
            $data['base_cash_left'] = $this->_webposCurrencyHelper->currencyConvert($data['base_cash_left'],$shiftBaseCurrencyCode, $baseCurrencyCode );
            $data['base_total_sales'] = $this->_webposCurrencyHelper->currencyConvert($data['base_total_sales'],$shiftBaseCurrencyCode, $baseCurrencyCode );
            $data['base_balance'] = $this->_webposCurrencyHelper->currencyConvert($data['base_balance'],$shiftBaseCurrencyCode, $baseCurrencyCode );
            $data['base_cash_sale'] = $this->_webposCurrencyHelper->currencyConvert($data['base_cash_sale'],$shiftBaseCurrencyCode, $baseCurrencyCode );
            $data['base_cash_added'] = $this->_webposCurrencyHelper->currencyConvert($data['base_cash_added'],$shiftBaseCurrencyCode, $baseCurrencyCode );
            $data['base_cash_removed'] = $this->_webposCurrencyHelper->currencyConvert($data['base_cash_removed'],$shiftBaseCurrencyCode, $baseCurrencyCode );
        }

        //stored of display currency code is different from the current display currency code at this time
        //convert all old display currency value to new display currency value
        if ($shiftCurrencyCode != $currentCurrencyCode){
            $data['shift_currency_code'] = $currentCurrencyCode;
            $data['float_amount'] = $this->_webposCurrencyHelper->currencyConvert($data['float_amount'],$shiftCurrencyCode, $currentCurrencyCode);
            $data['closed_amount'] = $this->_webposCurrencyHelper->currencyConvert($data['closed_amount'],$shiftCurrencyCode, $currentCurrencyCode);
            $data['cash_left'] = $this->_webposCurrencyHelper->currencyConvert($data['cash_left'],$shiftCurrencyCode, $currentCurrencyCode );
            $data['total_sales'] = $this->_webposCurrencyHelper->currencyConvert($data['total_sales'],$shiftCurrencyCode, $currentCurrencyCode);
            $data['balance'] = $this->_webposCurrencyHelper->currencyConvert($data['balance'],$shiftCurrencyCode, $currentCurrencyCode );
            $data['cash_sale'] = $this->_webposCurrencyHelper->currencyConvert($data['cash_sale'],$shiftCurrencyCode, $currentCurrencyCode);
            $data['cash_added'] = $this->_webposCurrencyHelper->currencyConvert($data['cash_added'],$shiftCurrencyCode, $currentCurrencyCode);
            $data['cash_removed'] = $this->_webposCurrencyHelper->currencyConvert($data['cash_removed'],$shiftCurrencyCode, $currentCurrencyCode);
        }

        $this->setData($data);
        $this->save();
        return $data;
    }

    /**
     * create datetime range from, to
     * @return array
     */
    public function getShiftTimeRangeFilter()
    {
        //filter just last 7 days shift by created_at
        $time = time();
        $to = date('Y-m-d H:i:s', $time);
        $lastTime = $time - 60 * 60 * 24 * 7;
        $from = date('Y-m-d H:i:s', $lastTime);
        return array('from' => $from, 'to' => $to);
    }

    /**
     * get detail information of a shift.
     * return data includes:
     * + All shift attribute that have getter and setter in Api/Data/Shift/ShiftInterface
     * + Sale summary data
     * + Cash Transaction Data
     * @param int $shift_id
     * @return mixed
     */
    public function detail($shiftId)
    {
        //return data
        $data = [];

        
        
        /** @var \Magestore\Webpos\Model\Shift\SaleSummary $saleSummaryModel */
        $saleSummaryModel = $this->_saleSummaryFactory->create();
        $saleSummaryData  = $saleSummaryModel->getSaleSummary($shiftId);

        /** @var \Magestore\Webpos\Model\Shift\CashTransaction $cashTransactionModel */
        $cashTransactionModel = $this->_cashTransactionFactory->create();
        $transactionData = $cashTransactionModel->getByShiftId($shiftId);
        $zReportSalesSummary = $saleSummaryModel->getZReportSalesSummary($shiftId);


        $data["sale_summary"] = $saleSummaryData;
        $data["transaction_data"] = $transactionData;
        $data["zreport_sales_summary"] = $zReportSalesSummary;

        return $data;
    }

    /**
     * get the latest open shift of a staff.
     * if there is no open shift, return 0
     * @param $staffId
     * @return int
     */
    public function getCurrentShiftId($staffId){

        $helperPermission = \Magento\Framework\App\ObjectManager::getInstance()->get('Magestore\Webpos\Helper\Permission');
        $shiftCollection = $this->getCollection();
        $shiftCollection->addFieldToFilter("staff_id", $staffId);
        $shiftCollection->addFieldToFilter("pos_id", $helperPermission->getCurrentPosId());
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

    public function recalculateData($newCashTransaction){

        $shiftId = $this->getShiftId();
        $balance = $this->getFloatAmount();
        $baseBalance = $this->getBaseFloatAmount();
        $cashAdded = 0;
        $cashSale = 0;
        $cashRemoved = 0;
        $cashRefunded = 0;
        $totalSales = 0;

        $cashTransactionData = $this->_cashTransactionFactory->create()->getByShiftId($shiftId);
        foreach ($cashTransactionData as $item){
            switch ($item['type']){
                case "add":
                    $cashAdded = $cashAdded + $item['value'];
                    $balance = $balance + $item['value'];
                    $baseBalance = $baseBalance + $item['base_value'];
                    break;
                case "remove":
                    $balance = $balance - $item['value'];
                    $cashRemoved = $cashRemoved + $item['value'];
                    $baseBalance = $baseBalance - $item['base_value'];
                    break;
                case "refund":
                    $balance = $balance - $item['value'];
                    $cashRefunded = $cashRefunded + $item['value'];
                    $baseBalance = $baseBalance - $item['base_value'];
                    break;
                case "order":
                    $balance = $balance + $item['value'];
                    $cashSale = $cashSale + $item['value'];
                    $baseBalance = $baseBalance + $item['base_value'];
                    $totalSales = $totalSales + $item['value'];
                    break;
            }
        }
        if($newCashTransaction){
            if($newCashTransaction->getType() == "remove"){
                $balance = $balance - $newCashTransaction->getValue();
                $baseBalance = $baseBalance - $newCashTransaction->getBaseValue();
                $cashRemoved = $cashRemoved + $newCashTransaction->getValue();
            } else if ($newCashTransaction['type'] != "refund"){
                $balance = $balance + $newCashTransaction->getValue();
                $baseBalance = $baseBalance + $newCashTransaction->getBaseValue();
                $cashAdded = $cashAdded + $newCashTransaction->getValue();
            }
        }


        $shiftCurrencyCode = $newCashTransaction->getData('transaction_currency_code');
        if(!$shiftCurrencyCode) {
            $shiftCurrencyCode = $this->getShiftCurrencyCode();
        }
        $shiftBaseCurrencyCode = $this->getBaseCurrencyCode();
        if(!$baseBalance) {
            $baseBalance = $this->_webposCurrencyHelper->currencyConvert($balance, $shiftCurrencyCode, $shiftBaseCurrencyCode);
        }

        $baseCashAdded = $this->_webposCurrencyHelper->currencyConvert($cashAdded, $shiftCurrencyCode, $shiftBaseCurrencyCode);
        $baseCashSale =  $this->_webposCurrencyHelper->currencyConvert($cashSale, $shiftCurrencyCode,$shiftBaseCurrencyCode);
        $baseCashRemoved = $this->_webposCurrencyHelper->currencyConvert($cashRemoved, $shiftCurrencyCode, $shiftBaseCurrencyCode);
        $baseCashRefunded = $this->_webposCurrencyHelper->currencyConvert($cashRefunded, $shiftCurrencyCode, $shiftBaseCurrencyCode);

//        $baseCashRemoved =  $this->_webposCurrencyHelper->currencyConvert($cashRemoved, $shiftCurrencyCode,$shiftBaseCurrencyCode);
//        $baseTotalSales = $this->_webposCurrencyHelper->currencyConvert($totalSales, $shiftCurrencyCode,$shiftBaseCurrencyCode);
        $this->setBalance($balance);
        $this->setBaseBalance($baseBalance);
        $this->setCashAdded($cashAdded);
        $this->setBaseCashAdded($baseCashAdded);
        $this->setCashSale($cashSale);
        $this->setBaseCashSale($baseCashSale);
        $this->setCashRemoved($cashRemoved);
        $this->setBaseCashRemoved($baseCashRemoved);
        $this->setCashRefunded($cashRefunded);
        $this->setBaseCashRefunded($baseCashRefunded);
//        $this->setBaseTotalSales($baseTotalSales);
//        $this->setTotalSales($totalSales);

        try {
            $this->save();
        } catch (\Exception $exception) {

            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $this->getData();
    }

    public function updateTotalSales($totalSales){
        $shiftCurrencyCode = $this->getShiftCurrencyCode();
        $shiftBaseCurrencyCode = $this->getBaseCurrencyCode();
        $baseTotalSales =  $this->_webposCurrencyHelper->currencyConvert($totalSales, $shiftCurrencyCode,$shiftBaseCurrencyCode);
        $this->setTotalSales($totalSales);
        $this->setBaseTotalSales($baseTotalSales);
        try {
            $this->save();
        } catch (\Exception $exception) {

            throw new CouldNotSaveException(__($exception->getMessage()));
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return \Magestore\Webpos\Api\Data\Shift\ShiftExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        $extensionAttributes = $this->_getExtensionAttributes();
        if (!$extensionAttributes) {
            return $this->extensionAttributesFactory->create('Magestore\Webpos\Api\Data\Shift\ShiftInterface');
        }
        return $extensionAttributes;
    }

    /**
     * {@inheritdoc}
     *
     * @param \Magestore\Webpos\Api\Data\Shift\ShiftExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Magestore\Webpos\Api\Data\Shift\ShiftExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}