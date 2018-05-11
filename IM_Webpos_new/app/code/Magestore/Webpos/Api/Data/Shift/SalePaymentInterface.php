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

/**
 * Interface CashTransactionInterface
 * @package Magestore\Webpos\Api\Data\Shift
 */
interface SalePaymentInterface
{
    /*#@+
     * Constants defined for keys of data array
     */
    const PAYMENT_METHOD = "payment_method";
    const PAYMENT_AMOUNT = "payment_amount";
    const BASE_PAYMENT_AMOUNT = "base_payment_amount";
    const METHOD_TITLE = "method_title";

    /**
     *  get payment method
     * @return string|null
     */
    public function getPaymentMethod();


    /**
     * Set payment method
     *
     * @param string $paymentMethod
     * @return $this
     */
    public function setPaymentMethod($paymentMethod);

    /**
     *  get payment amount
     * @return string|null
     */
    public function getPaymentAmount();


    /**
     * Set payment amount
     *
     * @param string $paymentAmount
     * @return $this
     */
    public function setPaymentAmount($paymentAmount);

    /**
     *  get base payment amount
     * @return string|null
     */
    public function getBasePaymentAmount();


    /**
     * Set base payment amount
     *
     * @param string $basePaymentAmount
     * @return $this
     */
    public function setBasePaymentAmount($basePaymentAmount);

    /**
     * Set method title
     *
     * @param string $methodTitle
     * @return $this
     */
    public function setMethodTitle($methodTitle);

    /**
     *  get method title
     * @return string|null
     */
    public function getMethodTitle();

}