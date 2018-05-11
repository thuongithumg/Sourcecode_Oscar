<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created by PhpStorm.
 * User: steve
 * Date: 07/06/2016
 * Time: 09:21
 */

namespace Magestore\Webpos\Api\Data\Shift;
use \Magestore\Webpos\Api\Data\IndexeddbInterface;

/**
 * Interface CashTransactionInterface
 * @package Magestore\Webpos\Api\Data\Shift
 */
interface CashTransactionInterface extends IndexeddbInterface
{
    /*#@+
     * Constants defined for keys of data array
     */
    const TRANSACTION_ID = "transaction_id";
    const LOCATION_ID = "location_id";
    const ORDER_ID = "order_id";
    const SHIFT_ID = "shift_id";
    const VALUE = "value";
    const BASE_VALUE = "base_value";
    const BALANCE = "balance";
    const BASE_BALANCE = "base_balance";
    const CREATED_AT = "created_at";
    const NOTE = "note";
    const TYPE = "type";
    const BASE_CURRENCY_CODE = "base_currency_code";
    const TRANSACTION_CURRENCY_CODE = "transaction_currency_code";
    const UPDATED_AT = "updated_at";
    const STAFF_ID = "staff_id";
    const STAFF_NAME = "staff_name";


    /**
     *  transaction id
     * @return int|null
     */
    public function getTransactionId();


    /**
     * Set Transaction Id
     *
     * @param int $transaction_id
     * @return $this
     */
    public function setTransactionId($transaction_id);

    /**
     *  shift id
     * @return string|null
     */
    public function getShiftId();


    /**
     * Set Shift Id
     *
     * @param string $shift_id
     * @return $this
     */
    public function setShiftId($shift_id);

    /**
     *  get created at
     * @return string|null created_at
     */
    public function getCreatedAt();

    /**
     *  set created at
     * @return string|null created_at
     */
    public function setCreatedAt($created_at);


    /**
     *  updated at
     * @return string|null updated_at
     */
    public function getUpdatedAt();


    /**
     *  updated time
     * @return string|null updated_at
     */
    public function setUpdatedAt($updated_at);

    /**
     * get value of the transaction
     * @return float
     */
    public function getValue();


    /**
     * set value of the transaction
     * @param float @value
     * @return $this
     */
    public function setValue($value);

    /**
     * get base_value of the transaction
     * @return float
     */
    public function getBaseValue();


    /**
     * set base_value of the transaction
     * @param float @value
     * @return $this
     */
    public function setBaseValue($base_value);


    /**
     * get balance of the shift at the time of this transaction
     * @return float
     */
    public function getBalance();

    /**
     *  set balance of the shift at the time of this transaction
     * @param float @balance
     * @return $this
     */
    public function setBalance($balance);

    /**
     *  set base_balance of the shift at the time of this transaction
     * @param float @base_balance
     * @return $this
     */
    public function setBaseBalance($base_balance);

    /**
     * get base_balance of the shift at the time of this transaction
     * @return float
     */
    public function getBaseBalance();

    /**
     * get note
     * @return string
     */
    public function getNote();

    /**
     * set note
     * @param string $note
     * @return $this
     */
    public function setNote($note);


    /**
     *  location id
     * @return int|null
     */
    public function getLocationId();


    /**
     * Set Location Id
     *
     * @param int $location_id
     * @return $this
     */
    public function setLocationId($location_id);


    /**
     *  order id
     * @return int|null
     */
    public function getOrderId();


    /**
     * Set Order Id
     *
     * @param int $order_id
     * @return $this
     */
    public function setOrderId($order);


    /**
     * get type of transaction: add, remove, order
     * add: add cash to cash drawer by manual
     * remove: remove cash from cash drawer by manual
     * order: add cash to cash drawer from order
     * @return string
     */
    public function getType();

    /**
     * set type
     * @param string $note
     * @return $this
     */
    public function setType($type);

    /**
     * get base currency code
     * @return string
     */
    public function getBaseCurrencyCode();

    /**
     * set base currency code
     * @param string $base_currency_code
     * @return $this
     */
    public function setBaseCurrencyCode($base_currency_code);

    /**
     * get shift currency code
     * @return string
     */
    public function getTransactionCurrencyCode();

    /**
     * set Transaction currency code
     * @param string $transaction_currency_code
     * @return $this
     */
    public function setTransactionCurrencyCode($transaction_currency_code);

    /**
     * get staff name
     * @return string
     */
    public function getStaffName();

    /**
     * set staff name
     * @param string $staffName
     * @return $this
     */
    public function setStaffName($staffName);


    /**
     *  staff id
     * @return int|null
     */
    public function getStaffId();


    /**
     * Set Staff Id
     *
     * @param int $staffId
     * @return $this
     */
    public function setStaffId($staffId);
}