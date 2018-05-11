<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Integration\Storecredit;

interface StoreCreditInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const CREDIT_ID = 'credit_id';
    const CUSTOMER_ID = 'customer_id';
    const CREDIT_BALANCE = 'credit_balance';
    /**#@-*/

    /**
     * Get credit ID
     *
     * @api
     * @return string
     */
    public function getCreditId();

    /**
     * Set credit ID
     *
     * @api
     * @param string $creditId
     * @return $this
     */
    public function setCreditId($creditId);

    /**
     * Get customer ID
     *
     * @api
     * @return string
     */
    public function getCustomerId();

    /**
     * Set customer ID
     *
     * @api
     * @param string $customerId
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * Get credit balance
     *
     * @api
     * @return string
     */
    public function getCreditBalance();

    /**
     * Set credit balance
     *
     * @api
     * @param string $creditBalance
     * @return $this
     */
    public function setCreditBalance($creditBalance);

}
