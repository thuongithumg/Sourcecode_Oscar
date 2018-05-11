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

use Magento\Framework\Api\ExtensibleDataInterface;
use \Magestore\Webpos\Api\Data\IndexeddbInterface;
/**
 * Interface ShiftInterface
 * @package Magestore\Webpos\Api\Data\Shift
 */
interface ShiftInterface extends ExtensibleDataInterface, IndexeddbInterface
{
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
    const CASH_REFUNDED = "cash_refunded";
    const BASE_CASH_REFUNDED = "base_cash_refunded";
    const SESSION = "session";
    const BASE_CURRENCY_CODE = "base_currency_code";
    const SHIFT_CURRENCY_CODE = "shift_currency_code";
    const UPDATED_AT = "updated_at";
    const POS_ID = "pos_id";
    const SALE_SUMMARY = "sale_summary";
    const CASH_TRANSACTION = "cash_transaction";
    const ZREPORT_SALES_SUMMARY = "zreport_sales_summary";
    const PROFIT_LOSS_REASON = "profit_loss_reason";


    /**
     *  entity id
     * @return int|null
     */
    public function getEntityId();


    /**
     * Set Entity Id
     *
     * @param string $entity_id
     * @return $this
     */
    public function setEntityId($entity_id);

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
     *  staff id
     * @return int|null
     */
    public function getStaffId();


    /**
     * Set Staff Id
     *
     * @param int $staff_id
     * @return $this
     */
    public function setStaffId($staff_id);

    /**
     *  location_id
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
     *  open time
     * @return string|null opened_at
     */
    public function getOpenedAt();


    /**
     *  opened time
     * @return string|null opened_at
     */
    public function setOpenedAt($openedAt);

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
     *  get closed at
     * @return string|null closed-at
     */
    public function getClosedAt();

    /**
     * set Closed At
     * @param string|null $closed_at
     * @return $this
     */
    public function setClosedAt($closed_at);

    /**
     * float amount when open shift
     * @return float
     */
    public function getFloatAmount();

    /**
     * Set Float Amount
     * @param float @float_amount
     * @return $this
     */
    public function setFloatAmount($float_amount);

    /**
     * base_float amount when open shift
     * @return float
     */
    public function getBaseFloatAmount();

    /**
     * Set Base Float Amount
     * @param float @base_float_amount
     * @return $this
     */
    public function setBaseFloatAmount($base_float_amount);

    /**
     * closed amount when open shift
     * @return float
     */
    public function getClosedAmount();

    /**
     * set Closed Amount
     * @param float @closed_amount
     * @return $this
     */
    public function setClosedAmount($closed_amount);

    /**
     * base closed amount when open shift
     * @return float
     */
    public function getBaseClosedAmount();

    /**
     * set Base Closed Amount
     * @param float @base_closed_amount
     * @return $this
     */
    public function setBaseClosedAmount($base_closed_amount);


    /**
     * status
     * @return int
     */
    public function getStatus();


    /**
     * set Status
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * cash left in cash drawer when close shift
     * @return float
     */
    public function getCashLeft();

    /**
     * set Cash Left
     * @param float @cash_left
     * @return $this
     */
    public function setCashLeft($cash_left);


    /**
     * base cash left in cash drawer when close shift
     * @return float
     */
    public function getBaseCashLeft();

    /**
     * set Base Cash Left
     * @param float @base_cash_left
     * @return $this
     */
    public function setBaseCashLeft($base_cash_left);
    /**
     * get closed note
     * @return string
     */
    public function getClosedNote();

    /**
     * set closed note
     * @param string $closed_note
     * @return $this
     */
    public function setClosedNote($closed_note);

    /**
     * get opened note
     * @return string
     */
    public function getOpenedNote();

    /**
     * set Opened note
     * @param string $opened_note
     * @return $this
     */
    public function setOpenedNote($opened_note);


    /**
     * get Total Sales
     * @return float
     */
    public function getTotalSales();


    /**
     * set Total Sales
     * @param float @total_sales
     * @return $this
     */
    public function setTotalSales($total_sales);

    /**
     * get Base Total Sales
     * @return float
     */
    public function getBaseTotalSales();


    /**
     * set Base Total Sales
     * @param float @base_total_sales
     * @return $this
     */
    public function setBaseTotalSales($base_total_sales);

    /**
     * get Balance
     * @return float
     */
    public function getBalance();


    /**
     * set Balance
     * @param float @balance
     * @return $this
     */
    public function setBalance($balance);

    /**
     * get Base Balance
     * @return float
     */
    public function getBaseBalance();


    /**
     * set Base Balance
     * @param float @base_balance
     * @return $this
     */
    public function setBaseBalance($base_balance);

    /**
     * get cash_sale
     * @return float
     */
    public function getCashSale();


    /**
     * set cash_sale
     * @param float @cash_sale
     * @return $this
     */
    public function setCashSale($cash_sale);

    /**
     * get base_cash_sale
     * @return float
     */
    public function getBaseCashSale();


    /**
     * set base_cash_sale
     * @param float @base_cash_sale
     * @return $this
     */
    public function setBaseCashSale($base_cash_sale);

    /**
     * get cash_added
     * @return float
     */
    public function getCashAdded();


    /**
     * set cash_added
     * @param float @cash_added
     * @return $this
     */
    public function setCashAdded($cash_added);

    /**
     * get base_cash_added
     * @return float
     */
    public function getBaseCashAdded();


    /**
     * set base_cash_added
     * @param float @base_cash_added
     * @return $this
     */
    public function setBaseCashAdded($base_cash_added);

    /**
     * get cash_removed
     * @return float
     */
    public function getCashRemoved();


    /**
     * set cash_removed
     * @param float @cash_removed
     * @return $this
     */
    public function setCashRemoved($cash_removed);

    /**
     * get base_cash_removed
     * @return float
     */
    public function getBaseCashRemoved();


    /**
     * set base_cash_removed
     * @param float @base_cash_removed
     * @return $this
     */
    public function setBaseCashRemoved($base_cash_removed);

    /**
     * get base_cash_refunded
     * @return float
     */
    public function getBaseCashRefunded();


    /**
     * set base_cash_refunded
     * @param float @base_cash_removed
     * @return $this
     */
    public function setBaseCashRefunded($base_cash_refunded);
    /**
     * get cash_refunded
     * @return float
     */
    public function getCashRefunded();


    /**
     * set base_cash_refunded
     * @param float @cash_removed
     * @return $this
     */
    public function setCashRefunded($cash_refunded);

    /**
     * @return mixed
     */
    public function getSession();

    /**
     * @param $session
     * @return mixed
     */
    public function setSession($session);

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
    public function getShiftCurrencyCode();

    /**
     * set shift currency code
     * @param string $shift_currency_code
     * @return $this
     */
    public function setShiftCurrencyCode($shift_currency_code);

    /**
     * get store id
     * @return string
     */
    public function getStoreId();

    /**
     * get pos name
     * @return string
     */
    public function getPosName();

    /**
     * get pos id
     * @return string
     */
    public function getPosId();

    /**
     * set pos id
     * @param string $posId
     * @return $this
     */
    public function setPosId($posId);

    /**
     * get sale summary
     * @return \Magestore\Webpos\Api\Data\Shift\SalePaymentInterface[]
     */
    public function getSaleSummary();

    /**
     * get cash transaction
     * @return \Magestore\Webpos\Api\Data\Shift\CashTransactionInterface[]
     */
    public function getCashTransaction();

    /**
     * get zreport sales summary
     * @return \Magestore\Webpos\Api\Data\Shift\SaleSummary
     */
    public function getZreportSalesSummary();

    /**
     * set reason
     * @param string $reason
     * @return $this
     */
    public function setProfitLossReason($reason);

    /**
     * get reason
     * @return string
     */
    public function getProfitLossReason();

    /**
     * get staff name
     * @return string
     */
    public function getStaffName();

    /**
     * get staff name
     * @return string
     */
    public function getOpener();

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Shift\ShiftExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Shift\ShiftExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Magestore\Webpos\Api\Data\Shift\ShiftExtensionInterface $extensionAttributes);

}