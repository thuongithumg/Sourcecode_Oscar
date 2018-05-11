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

use \Magento\Framework\Model\AbstractModel as AbstractModel;
use \Magestore\Webpos\Api\Data\Shift\CashTransactionInterface as CashTransactionInterface;

/**
 * Class Transaction
 * @package Magestore\Webpos\Model\CashTransaction
 */

class CashTransaction extends AbstractModel implements CashTransactionInterface
{

    /** @var   $staffFactory \Magestore\Webpos\Model\Staff\StaffFactory $staffFactory */
    protected $_staffFactory;

    /** @var  \Magestore\Webpos\Helper\Currency */
    protected $_webposCurrencyHelper;




    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\Webpos\Helper\Currency $webposCurrencyHelper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,

        array $data = []
    ) {
        $this->_webposCurrencyHelper = $webposCurrencyHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\ResourceModel\Shift\CashTransaction');
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
     *  transaction id
     * @return int|null
     */
    public function getTransactionId()
    {
        return $this->getData(self::TRANSACTION_ID);
    }


    /**
     * Set Transaction Id
     *
     * @param int $transaction_id
     * @return $this
     */
    public function setTransactionId($transaction_id)
    {
        return $this->setData(self::TRANSACTION_ID, $transaction_id);
    }

    /**
     *  shift id
     * @return string|null
     */
    public function getShiftId()
    {
        return $this->getData(self::SHIFT_ID);
    }

    /**
     * Set Shift Id
     *
     * @param string $shift_id
     * @return $this
     */
    public function setShiftId($shift_id)
    {
        return $this->setData(self::SHIFT_ID, $shift_id);
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
     * Set location Id
     *
     * @param int $location_id
     * @return $this
     */
    public function setLocationId($location_id)
    {
        return $this->setData(self::LOCATION_ID, $location_id);
    }


    /**
     *  order id
     * @return int|null
     */
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * Set Order Id
     *
     * @param int $order_id
     * @return $this
     */
    public function setOrderId($order_id)
    {
        return $this->setData(self::ORDER_ID, $order_id);
    }

    /**
     *  get created at
     * @return string|null created_at
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     *  set created at
     * @return string|null created_at
     */
    public function setCreatedAt($created_at)
    {
        return $this->setData(self::CREATED_AT, $created_at);
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
     * get value of the transaction
     * @return float
     */
    public function getValue()
    {
        return $this->getData(self::VALUE);
    }


    /**
     * set value of the transaction
     * @param float @value
     * @return $this
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * get base_value of the transaction
     * @return float
     */
    public function getBaseValue()
    {
        return $this->getData(self::BASE_VALUE);
    }

    /**
     * set base_value of the transaction
     * @param float @base_value
     * @return $this
     */
    public function setBaseValue($base_value)
    {
        return $this->setData(self::BASE_VALUE, $base_value);
    }

    /**
     * get balance of the shift at the time of this transaction
     * @return float
     */
    public function getBalance()
    {
        return $this->getData(self::BALANCE);
    }

    /**
     *  set balance of the shift at the time of this transaction
     * @param float @balance
     * @return $this
     */
    public function setBalance($balance)
    {
        return $this->setData(self::BALANCE, $balance);
    }

    /**
     * get base_balance of the shift at the time of this transaction
     * @return float
     */
    public function getBaseBalance()
    {
        return $this->getData(self::BASE_BALANCE);
    }

    /**
     *  set base_balance of the shift at the time of this transaction
     * @param float @base_balance
     * @return $this
     */
    public function setBaseBalance($base_balance)
    {
        return $this->setData(self::BASE_BALANCE, $base_balance);
    }

    /**
     * get note
     * @return string
     */
    public function getNote()
    {
        return $this->getData(self::NOTE);
    }

    /**
     * set note
     * @param string $note
     * @return $this
     */
    public function setNote($note)
    {
        return $this->setData(self::NOTE, $note);
    }

    /**
     * get type
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * set type
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
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
    public function setBaseCurrencyCode($base_currency_code){
        return $this->setData(self::BASE_CURRENCY_CODE,$base_currency_code);
    }

    /**
     * get Transaction currency code
     * @return string
     */
    public function getTransactionCurrencyCode(){
        return $this->getData(self::TRANSACTION_CURRENCY_CODE);
    }

    /**
     * set transaction currency code
     * @param string $transaction_currency_code
     * @return $this
     */
    public function setTransactionCurrencyCode($transaction_currency_code){
        return $this->setData(self::TRANSACTION_CURRENCY_CODE,$transaction_currency_code);
    }
    
    
    /**
     * get staff name
     * @return string
     */
    public function getStaffName()
    {
        return $this->getData(self::STAFF_NAME);
    }

    /**
     * set staff name
     * @param string $staff_name
     * @return $this
     */
    public function setStaffName($staff_name)
    {
        return $this->setData(self::STAFF_NAME, $staff_name);
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
     * get all transactions for a shift has shift_id
     * @param string $shift_id
     * @return mixed
     */
    public function getByShiftId($shift_id)
    {
        $data = [];

        $collection = $this->getCollection();
        $collection->addFieldToFilter("shift_id", $shift_id);

        foreach ($collection as $item) {
            $itemData = $this->convertDataToCurrentCurrency($item->getData());
            $data[] = $itemData;
        }

        return $data;
    }

    /** convert transaction data to current currency value
     * @param $data
     * @return mixed
     */
    public function convertDataToCurrentCurrency($data){
        $transactionBaseCurrencyCode = $data['base_currency_code'];
        $TransactionCurrencyCode = $data['transaction_currency_code'];
        $baseCurrencyCode = $this->_webposCurrencyHelper->getBaseCurrencyCode();
        $currentCurrencyCode = $this->_webposCurrencyHelper->getCurrentCurrencyCode();
        //convert all data of $shiftBaseCurrencyCode to $baseCurrencyCode
        if($transactionBaseCurrencyCode != $baseCurrencyCode){
            $data['base_currency_code'] = $baseCurrencyCode;
            $data['base_value'] = $this->_webposCurrencyHelper->currencyConvert($data['base_value'],$transactionBaseCurrencyCode, $baseCurrencyCode );
            $data['base_balance'] = $this->_webposCurrencyHelper->currencyConvert($data['base_balance'],$transactionBaseCurrencyCode, $baseCurrencyCode );

        }
        //convert all data of $shiftCurrencyCode to $currentCurrencyCode
        if ($TransactionCurrencyCode != $currentCurrencyCode){
            $data['shift_currency_code'] = $currentCurrencyCode;
            $data['value'] = $this->_webposCurrencyHelper->currencyConvert($data['value'],$TransactionCurrencyCode, $currentCurrencyCode);
            $data['balance'] = $this->_webposCurrencyHelper->currencyConvert($data['balance'],$TransactionCurrencyCode, $currentCurrencyCode);

        }
        return $data;
    }
}