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

class Magestore_Webpos_Model_Shift extends Mage_Core_Model_Abstract {
    /*#@+
     * Constants defined for keys of data array
     */
    const ENTITY_ID = "entity_id";
    const SHIFT_ID = "shift_id";
    const STAFF_ID = "staff_id";
    const STAFF_NAME = "staff_name";
    const LOCATION_ID = "location_id";
    const OPENED_AT = "opened_at";
    const CLOSED_AT = "closed_at";
    const FLOAT_AMOUNT = "float_amount";
    const BASE_FLOAT_AMOUNT = "base_float_amount";
    const CLOSED_AMOUNT = "closed_amount";
    const BASE_CLOSED_AMOUNT = "base_closed_amount";
    const STATUS = "status";
    const CASH_LEFT = "cash_left";
    const BASE_CASH_LEFT = "base_cash_left";
    const CLOSED_NOTE = "closed_note";
    const TOTAL_SALES = "total_sales";
    const BASE_TOTAL_SALES = "base_total_sales";
    const BALANCE = "balance";
    const BASE_BALANCE = "base_balance";
    const OPENED_NOTE = "opened_note";
    const CASH_SALE = "cash_sale";
    const BASE_CASH_SALE = "base_cash_sale";
    const CASH_ADDED = "cash_added";
    const BASE_CASH_ADDED = "base_cash_added";
    const CASH_REMOVED = "cash_removed";
    const BASE_CASH_REMOVED = "base_cash_removed";
    const REFUND_AMOUNT = "refund_amount";
    const BASE_REFUND_AMOUNT = "base_refund_amount";
    const SESSION = "session";
    const BASE_CURRENCY_CODE = "base_currency_code";
    const SHIFT_CURRENCY_CODE = "shift_currency_code";
    const UPDATED_AT = "updated_at";
    const POS_ID = "pos_id";
    const SALE_SUMMARY = "sale_summary";
    const CASH_TRANSACTION = "cash_transaction";
    const ZREPORT_SALES_SUMMARY = "zreport_sales_summary";
    const PROFIT_LOSS_REASON = "profit_loss_reason";
    const INDEXEDDB_ID = "indexeddb_id";

    /**
     * Model event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'webpos_shift';

    /**
     * Name of the event object
     *
     * @var string
     */
    protected $_eventObject = 'data_object';

    public function _construct() {
        parent::_construct();
        $this->_init('webpos/shift');
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
     * set baseCashRemoved
     * @param float @baseCashRemoved
     * @return $this
     */
    public function setRefundAmount($baseCashRemoved) {
        return $this->setData(self::REFUND_AMOUNT, $baseCashRemoved);
    }

    /**
     * set baseCashRemoved
     * @param float @baseCashRemoved
     * @return $this
     */
    public function setBaseRefundAmount($baseCashRemoved) {
        return $this->setData(self::BASE_REFUND_AMOUNT, $baseCashRemoved);
    }

    /** get Session
     * @return string
     */
    public function getRefundAmount() {
        return $this->getData(self::REFUND_AMOUNT);
    }

    /** get Session
     * @return string
     */
    public function getBaseRefundAmount() {
        return $this->getData(self::BASE_REFUND_AMOUNT);
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
        if($posId) {
            $pos = Mage::getModel('webpos/pos')->load($posId);
            $posName = $pos->getPosName();
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
        if($staffId) {
            $staff = Mage::getModel('webpos/user')->load($staffId);
            $staffName = $staff->getData('display_name');
            $staffName = ($staffName)?$staffName:$staff->getUsername();
        }
        return $staffName;
    }

    /**
     * get pos name
     * @return string
     */
    public function getStoreId()
    {
        $posId = $this->getData(self::POS_ID);
        $storeId = '';
        if($posId) {
            $pos = Mage::getModel('webpos/pos')->load($posId);
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
        $saleSummaryModel = Mage::getModel('webpos/saleSummary');
        $saleSummaryData = $saleSummaryModel->getSaleSummary($this->getShiftId());
        return $saleSummaryData;
    }

    /**
     * get cash transaction
     * @return array
     */
    public function getCashTransaction()
    {
        $cashTransactionModel = Mage::getModel('webpos/shift_cashtransaction');
        $transactionData = $cashTransactionModel->getByShiftId($this->getShiftId());
        return $transactionData;
    }

    /**
     * get zreport sales summary
     */
    public function getZreportSalesSummary()
    {
        $saleSummaryModel = Mage::getModel('webpos/saleSummary');
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
        $staffModel = Mage::getModel('webpos/user')->load($staffId);
        $saleSummaryModel = Mage::getModel('webpos/saleSummary');
        $cashTransactionModel = Mage::getModel('webpos/shift_cashtransaction');
        $shiftCollection = $this->getCollection();
        $shiftCollection->addFieldToFilter("staff_id", $staffId);
        $shiftCollection->addFieldToFilter('opened_at', $this->getShiftTimeRangeFilter());
        $shiftCollection->setOrder("shift_id", "DESC");
        $data = array();
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
     * @return array
     */
    public function getInfo()
    {
        $saleSummaryModel = Mage::getModel('webpos/saleSummary');
        $cashTransactionModel = Mage::getModel('webpos/shift_cashtransaction');
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
        $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
        $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
        //no thing change on currency, so we don't need to convert data.
        if(($shiftBaseCurrencyCode == $baseCurrencyCode) && ($shiftCurrencyCode == $currentCurrencyCode)){
            return $data;
        }
        //stored baseCurrencyCode is different from the base currency code now
        //convert all old base currency value to new base currency value
        if($shiftBaseCurrencyCode != $baseCurrencyCode){
            $data['base_currency_code'] = $baseCurrencyCode;
            $data['base_float_amount'] = Mage::helper('directory')->currencyConvert($data['base_float_amount'],$shiftBaseCurrencyCode, $baseCurrencyCode );
            $data['base_closed_amount'] = Mage::helper('directory')->currencyConvert($data['base_closed_amount'],$shiftBaseCurrencyCode, $baseCurrencyCode );
            $data['base_cash_left'] = Mage::helper('directory')->currencyConvert($data['base_cash_left'],$shiftBaseCurrencyCode, $baseCurrencyCode );
            $data['base_total_sales'] = Mage::helper('directory')->currencyConvert($data['base_total_sales'],$shiftBaseCurrencyCode, $baseCurrencyCode );
            $data['base_balance'] = Mage::helper('directory')->currencyConvert($data['base_balance'],$shiftBaseCurrencyCode, $baseCurrencyCode );
            $data['base_cash_sale'] = Mage::helper('directory')->currencyConvert($data['base_cash_sale'],$shiftBaseCurrencyCode, $baseCurrencyCode );
            $data['base_cash_added'] = Mage::helper('directory')->currencyConvert($data['base_cash_added'],$shiftBaseCurrencyCode, $baseCurrencyCode );
            $data['base_cash_removed'] = Mage::helper('directory')->currencyConvert($data['base_cash_removed'],$shiftBaseCurrencyCode, $baseCurrencyCode );
        }

        //stored of display currency code is different from the current display currency code at this time
        //convert all old display currency value to new display currency value
        if ($shiftCurrencyCode != $currentCurrencyCode){
            $data['shift_currency_code'] = $currentCurrencyCode;
            $data['float_amount'] = Mage::helper('directory')->currencyConvert($data['float_amount'],$shiftCurrencyCode, $currentCurrencyCode);
            $data['closed_amount'] = Mage::helper('directory')->currencyConvert($data['closed_amount'],$shiftCurrencyCode, $currentCurrencyCode);
            $data['cash_left'] = Mage::helper('directory')->currencyConvert($data['cash_left'],$shiftCurrencyCode, $currentCurrencyCode );
            $data['total_sales'] = Mage::helper('directory')->currencyConvert($data['total_sales'],$shiftCurrencyCode, $currentCurrencyCode);
            $data['balance'] = Mage::helper('directory')->currencyConvert($data['balance'],$shiftCurrencyCode, $currentCurrencyCode );
            $data['cash_sale'] = Mage::helper('directory')->currencyConvert($data['cash_sale'],$shiftCurrencyCode, $currentCurrencyCode);
            $data['cash_added'] = Mage::helper('directory')->currencyConvert($data['cash_added'],$shiftCurrencyCode, $currentCurrencyCode);
            $data['cash_removed'] = Mage::helper('directory')->currencyConvert($data['cash_removed'],$shiftCurrencyCode, $currentCurrencyCode);
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
        $data = array();
        /** @var \Magestore\Webpos\Model\Shift\SaleSummary $saleSummaryModel */
        $saleSummaryModel = Mage::getModel('webpos/saleSummary');
        $saleSummaryData  = $saleSummaryModel->getSaleSummary($shiftId);
        /** @var \Magestore\Webpos\Model\Shift\CashTransaction $cashTransactionModel */
        $cashTransactionModel = Mage::getModel('webpos/shift_cashtransaction');
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
        $helperPermission = Mage::helper('webpos/permission');
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

    public function recalculateData($cashTransaction){
        if (isset ($cashTransaction['cashTransaction'])) {
            $newCashTransaction = $cashTransaction['cashTransaction'];
            $shiftId = isset($newCashTransaction['shift_id'])?$newCashTransaction['shift_id']:0;
            $balance = $this->getFloatAmount();
            $baseBalance = $this->getBaseFloatAmount();
            $cashAdded = 0;
            $cashSale = 0;
            $cashRemoved = 0;
            $totalSales = 0;
            $totalRefund = 0;
            $cashTransactionData = Mage::getModel('webpos/shift_cashtransaction')->getByShiftId($shiftId);;

            if ($cashTransactionData) {
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
                        case "order":
                            $balance = $balance + $item['value'];
                            $cashSale = $cashSale + $item['value'];
                            $baseBalance = $baseBalance + $item['base_value'];
                            $totalSales = $totalSales + $item['value'];
                            break;
                        case 'refund':
                            $balance = $balance - $item['value'];
                            $baseBalance = $baseBalance - $item['base_value'];
                            $totalRefund = $totalRefund + $item['value'];
                            break;
                    }
                }
            }

            if($newCashTransaction){
                if ($newCashTransaction['type'] == "remove"){
                    $balance = $balance - $newCashTransaction['value'];
                    $baseBalance = $baseBalance - $newCashTransaction['base_value'];
                    $cashRemoved = $cashRemoved + $newCashTransaction['value'];
                } else if ($newCashTransaction['type'] == "refund"){
//                    $balance = $balance - $newCashTransaction['value'];
//                    $baseBalance = $baseBalance - $newCashTransaction['base_value'];
//                    $totalRefund += $newCashTransaction['value'];
                } else {
                    $balance = $balance + $newCashTransaction['value'];
                    $baseBalance = $baseBalance + $newCashTransaction['base_value'];
                    $cashAdded = $cashAdded + $newCashTransaction['value'];
                }
            }

            $shiftCurrencyCode = $newCashTransaction['transaction_currency_code'];
            $shiftBaseCurrencyCode = $newCashTransaction['base_currency_code'];

            if(!$baseBalance) {
                $baseBalance = Mage::helper('directory')->currencyConvert($balance, $shiftCurrencyCode, $shiftBaseCurrencyCode);
            }

            $baseCashAdded = Mage::helper('directory')->currencyConvert($cashAdded, $shiftCurrencyCode, $shiftBaseCurrencyCode);
            $baseCashSale =  Mage::helper('directory')->currencyConvert($cashSale, $shiftCurrencyCode, $shiftBaseCurrencyCode);
            $baseCashRemoved = Mage::helper('directory')->currencyConvert($cashRemoved, $shiftCurrencyCode, $shiftBaseCurrencyCode);
            $baseTotalRefund = Mage::helper('directory')->currencyConvert($totalRefund, $shiftCurrencyCode, $shiftBaseCurrencyCode);

            $this->setBalance($balance);
            $this->setBaseBalance($baseBalance);
            $this->setCashAdded($cashAdded);
            $this->setBaseCashAdded($baseCashAdded);
            $this->setCashSale($cashSale);
            $this->setBaseCashSale($baseCashSale);
            $this->setCashRemoved($cashRemoved);
            $this->setBaseCashRemoved($baseCashRemoved);
            $this->setRefundAmount($totalRefund);
            $this->setBaseRefundAmount($baseTotalRefund);
            $this->save();
            return $this->getData();
        }
    }

    public function updateTotalSales($totalSales){
        $shiftCurrencyCode = $this->getShiftCurrencyCode();
        $shiftBaseCurrencyCode = $this->getBaseCurrencyCode();
        $baseTotalSales =  Mage::helper('directory')->currencyConvert($totalSales, $shiftCurrencyCode,$shiftBaseCurrencyCode);
        $this->setTotalSales($totalSales);
        $this->setBaseTotalSales($baseTotalSales);
        try {
            $this->save();
        } catch (\Exception $exception) {
        }
    }

}
