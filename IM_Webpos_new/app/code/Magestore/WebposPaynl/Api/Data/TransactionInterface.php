<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposPaynl\Api\Data;

/**
 * Interface TransactionInterface
 * @package Magestore\WebposPaynl\Api|Data
 */
interface TransactionInterface
{

    /**#@+
     * Constants for field names
     */
    const QUOTE_ID = 'quote_id';
    const DESCRIPTION = 'description';
    const TOTAL = 'total';
    const CURRENCY = 'currency';
    const AMOUNT = 'amount';
    const BANK_ID = 'bank_id';
    /**#@-*/

    /**
     * Get quote id
     *
     * @api
     * @return float|null
     */
    public function getQuoteId();

    /**
     * Set quote id
     *
     * @api
     * @param float $orderId
     * @return $this
     */
    public function setQuoteId($quoteId);

    /**
     * Get description
     *
     * @api
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     *
     * @api
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * Set total
     *
     * @api
     * @param float $total
     * @return $this
     */
    public function setTotal($total);

    /**
     * Get total
     *
     * @api
     * @return float|null
     */
    public function getTotal();

    /**
     * Set currency
     *
     * @api
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency);

    /**
     * Get currency
     *
     * @api
     * @return string|null
     */
    public function getCurrency();

    /**
     * Set amount
     *
     * @api
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount);

    /**
     * Get amount
     *
     * @api
     * @return float|null
     */
    public function getAmount();

    /**
     * Set bank id
     *
     * @api
     * @param string $bankId
     * @return $this
     */
    public function setBankId($bankId);

    /**
     * Get bank id
     *
     * @api
     * @return string|null
     */
    public function getBankId();

}
