<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Directory\Currency;

interface CurrencyInterface
{
    /**
     * Get items.
     *
     * @return \Magestore\Webpos\Api\Data\Directory\Currency\CurrencyInterface[] Array of collection items.
     */

    /**#@+
     * Constants for keys of data array
     */
    const CODE = 'code';
    const CURRENCY_NAME = 'currency_name';
    const CURRENCY_RATE = 'currency_rate';
    const CURRENCY_SYMBOL = 'currency_symbol';
    const IS_DEFAULT = 'is_default';
    /**#@-*/

    /**
     * Get code
     *
     * @api
     * @return string
     */
    public function getCode();

    /**
     * Set code
     *
     * @api
     * @param string $code
     * @return $this
     */
    public function setCode($code);

    /**
     * Get currency name
     *
     * @api
     * @return string
     */
    public function getCurrencyName();

    /**
     * Set currency name
     *
     * @api
     * @param string $currencyTo
     * @return $this
     */
    public function setCurrencyName($name);

    /**
     * Get currency rate
     *
     * @api
     * @return string|null
     */
    public function getCurrencyRate();

    /**
     * Set currency rate
     *
     * @api
     * @param string $currencyRate
     * @return $this
     */
    public function setCurrencyRate($currencyRate);

    /**
     * Get currency symbol
     *
     * @api
     * @return string
     */
    public function getCurrencySymbol();

    /**
     * Set currency symbol
     *
     * @api
     * @param string $currencySymbol
     * @return $this
     */
    public function setCurrencySymbol($currencySymbol);

    /**
     * Get is default
     *
     * @api
     * @return string
     */
    public function getIsDefault();

    /**
     * Set is default
     *
     * @api
     * @param string $isDefault
     * @return $this
     */
    public function setIsDefault($isDefault);

}
