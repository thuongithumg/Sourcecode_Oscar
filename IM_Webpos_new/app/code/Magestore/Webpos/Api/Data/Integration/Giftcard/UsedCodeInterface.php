<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Integration\Giftcard;

interface UsedCodeInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const CODE = 'code';
    const AMOUNT = 'amount';
    const BALANCE = 'balance';
    /**#@-*/

    /**
     * Get code
     *
     * @api
     * @return string|null
     */
    public function getCode();

    /**
     * Get amount
     *
     * @api
     * @return string|null
     */
    public function getAmout();

    /**
     * Get balance
     *
     * @api
     * @return string|null
     */
    public function getBalance();

    /**
     * Get code
     *
     * @api
     * @param string|null
     */
    public function setCode($code);

    /**
     * Get amount
     *
     * @api
     * @param string|null
     */
    public function setAmout($amount);

    /**
     * Get balance
     *
     * @api
     * @param string|null
     */
    public function setBalance($balance);
}
