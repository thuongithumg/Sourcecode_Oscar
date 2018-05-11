<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Shipping;

interface ShippingInterface
{
    /**
     * Get items.
     *
     * @return \Magestore\Webpos\Api\Data\Shipping\ShippingInterface[] Array of collection items.
     */

    /**#@+
     * Constants for keys of data array
     */
    const CODE = 'code';
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    const ERROR_MESSAGE = 'error_message';
    const PRICE_TYPE = 'price_type';
    const PRICE = 'price';
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
     * Get title
     *
     * @api
     * @return string|null
     */
    public function getTitle();

    /**
     * Set title
     *
     * @api
     * @param string $title
     * @return $this
     */
    public function setTitle($title);

    /**
     * Get information
     *
     * @api
     * @return string|null
     */
    public function getDescription();

    /**
     * Set information
     *
     * @api
     * @param string $information
     * @return $this
     */
    public function setDescription($description);

    /**
     * Get error message
     *
     * @api
     * @return string|null
     */
    public function getErrorMessage();

    /**
     * Set error message
     *
     * @api
     * @param string $errorMessage
     * @return $this
     */
    public function setErrorMessage($errorMessage);


    /**
     * Get price type
     *
     * @api
     * @return string
     */
    public function getPriceType();

    /**
     * Set price type
     *
     * @api
     * @param string priceType
     * @return $this
     */
    public function setPriceType($priceType);

    /**
     * Get price
     *
     * @api
     * @return string
     */
    public function getPrice();

    /**
     * Set price
     *
     * @api
     * @param string price
     * @return $this
     */
    public function setPrice($price);

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
     * @param string price
     * @return $this
     */
    public function setIsDefault($isDefault);

}
